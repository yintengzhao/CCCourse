<?php

/**
 * @todo
 * COUNT 表达式
 * JOIN
 * GRUOP BY
 */
class Model implements JsonSerializable, ArrayAccess
{
	/**
	 * 数据库表名
	 * @var string
	 */
	protected $table;

	/**
	 * 主键
	 * @var string
	 */
	protected $pk = 'id';

	/**
	 * 参数变量ID
	 * @var integer
	 */
	protected $param_id=0;
	// protected $id = ;

	// protected $has_tables        = array();
	// protected $belongs_to_tables = array();
	protected $join_tables       = array();
	protected $fields            = array(); //查询字段
	protected $group_fields      = array(); //分组字段
	protected $data              = array(); //数据
	protected $condition         = array(); //查询条件
	protected $param             = array(); //查询参数
	protected $distinct          = false;   //是否去重
	protected $order             = array(); //排序字段
	protected $limit             = null;


	protected static $_db = null; //数据库连接

	const OPERATOR = array(
		'sym'=>array('=','>','>=','<','<=','<>'),
		'exp'=>array('LIKE','IN','BETWEEN','LIKE BINARY'),
		);

	const EXP =array('ABS','MAX', 'MIN', 'AVG', 'SUM','COUNT','SIGN','FLOOR','CEILING'); 

	public function __construct($table, $pk = 'id')
	{
		$this->table = $table;
		$this->pk    = $pk;
		if (self::$_db == null)
		{
			$config    = Config::getSecret('database');
			$dsn       = $config['driver'] . ':host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['database'];
			self::$_db = new Service\Db($dsn, $config['username'], $config['password']);
		}
	}

	/**
	 * has
	 * 拥有的内容，参数$table的外键是此表的主键
	 * @method one
	 * @param  [type] $table [description]
	 * @param  [type] $fk    [description]
	 * @return [type]        [description]
	 * @author NewFuture
	 */
	public function has($table, $fk = null)
	{
		if ($fk == null)
		{
			$fk = $this->table . '_id';
		}
		// $this->has_tables[$table]=$fk;
		$this->join_tables[]= array(
			'LEFT',
			$table,
			self::qouteField($fk,$table),
			self::backQoute($this->table).'.'.self::backQoute('id'),
			);
		return $this;
	}

	/**
	 * 从属关系[inner join]
	 * 参数表的主键是此表的外键
	 * @method belongs
	 * @param  [string]  $table [表名]
	 * @param  [string]  [$fk]    [$table于此表关联的外键]
	 * @return [object]         [description]
	 * @author NewFuture
	 */
	public function belongs($table, $fk = null)
	{
		if ($fk == null)
		{
			$fk = $table . '_id';
		}
		// $this->belongs_to_tables[$fk] = $table;
		$this->join_tables[]= array(
			'INNER',
			$table,
			self::qouteField($fk,$this->table),
			self::backQoute($table).'.'.self::backQoute('id'),
			);
		return $this;
	}

	/**
	 * 单条查询
	 * @method find
	 * @param  [mixed] $id [description]
	 * @return [array]     [结果数组]
	 * @author NewFuture
	 * @example
	 * 	find(1);//查找主键为1的结果
	 * 	find(['name'=>'ha'])//查找name为ha的结果
	 */
	public function find($id = null, $value = null)
	{
		if (null !== $value)
		{
			$this->data[$id] = $value;
		}
		elseif (null != $id)
		{
			if (is_array($id))
			{
				$this->data = array_merge($this->data, $id);
			}
			else
			{
				$this->data[$this->pk] = $id;
			}
		}
		$this->limit = 1;
		$result      = $this->select() ?: null;
		if(isset($result[0]))
		{
			$result      = $result[0] ;
		}
		$this->data  = $result;
		if (is_numeric($id))
		{
			$this->data[$this->pk] = $id;
		}
		return $result;
	}

	/**
	 * 批量查询
	 * @method select
	 * @param  array  $data  [查询数据条件]
	 * @return [array]       [结果数组]
	 * @author NewFuture
	 */
	public function select($data = array())
	{
		if (is_array($data))
		{
			//数组条件
			$this->data = array_merge($this->data, $data);
		}
		elseif (is_string($data))
		{
			//select筛选字段
			$this->field($data);
		}

		$sql = $this->buildSelectSql();
		$sql .= $this->buildFromSql();
		$sql .= $this->buildWhereSql();
		$sql .= $this->buildTailSql();
		return $this->query($sql, $this->param);
	}

	/**
	 * 保存数据
	 * @method save
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 * @author NewFuture
	 */
	public function save($data = array())
	{
		if (is_numeric($data))
		{
			$this->where($this->pk, '=', $data);
			$data       = $this->data;
			$this->data = array();
		}
		elseif (empty($data))
		{
			$data       = $this->data;
			$this->data = array();
		}
		return $this->update($data);
	}

	/**
	 * 更新数据
	 * @method update
	 * @param  array  $data  [要更新的数据]
	 * @return [array]       [结果数组]
	 * @author NewFuture
	 */
	public function update($data = array())
	{
		//字段过滤
		$fields = $this->fields;
		if (!empty($fields))
		{
			$fields = array_flip($fields);
			$data   = array_intersect_key($data, $field);
		}

		if (is_array($data))
		{

			$update_string = '';
			foreach ($data as $key => $v)
			{
				$update_string .= self::backQoute($key) . '=' . $this->setParam($v) . ',';
			}
			$update_string = trim($update_string, ',');
		}
		elseif (is_string($data))
		{
			$update_string = $data;
		}
		else
		{
			if (Config::get('debug'))
			{
				throw new Exception('未知参数', 1);
			}
			return false;
		}

		$sql = 'UPDATE' . self::backQoute($this->table);
		$sql .= 'SET' . $update_string;

		if (empty($this->condition) && empty($this->data) && isset($data[$this->pk]))
		{
			/*默认使用data主键作为更新值*/
			$this->data[$this->pk] = $data[$this->pk];
		}
		$sql .= $this->buildWhereSql();

		return $this->execute($sql, $this->param);
	}

	/**
	 * 新增数据
	 * 合并现有的data属性
	 * @method add
	 * @param  array $data [要更新的数据]
	 * @author NewFuture
	 */
	public function add($data = array())
	{
		if (is_array($data))
		{
			$this->data = array_merge($this->data, $data);
		}
		$data = $this->data;
		return $this->insert($data);
	}

	/**
	 * 插入数据库
	 * 自动忽略前置条件
	 * @method insert
	 * @param  [array] $data 	[要更新的数据]
	 * @return [integer]        [返回插入行id]
	 * @author NewFuture
	 */
	public function insert($data = array())
	{
		//字段过滤
		$fields = $this->fields;
		if (!empty($fields))
		{
			$fields = array_flip($fields);
			$data   = array_intersect_key($data, $field);
		}
		//插入数据
		if (!empty($data))
		{
			// $this->fields = $data;
			$fields       = array_keys($data);
			$quote_fields = $fields;
			//对字段进行转义
			array_walk($quote_fields, function (&$k)
			{
				$k = self::backQoute($k);
			});
			$sql = 'INSERT INTO' . self::backQoute($this->table);
			$sql .= '(' . implode(',', $quote_fields) . ')VALUES(:' . implode(',:', $fields) . ')';
			$this->execute($sql, $data);
			return self::$_db->lastInsertId();
		}
	}

	/**
	 * 批量插入数据
	 * 忽略前置条件
	 * @method insertAll
	 * @param  array    $fields       [字段]
	 * @param  array     $values      [数据，二维数组]
	 * @return int 插入条数
	 * @author NewFuture
	 */
	public function insertAll($fields, $values = array())
	{
		//字段过滤
		$quote_fields = array_map(array($this, 'backQoute'), $fields);

		//插入数据
		$sql = 'INSERT INTO' . self::backQoute($this->table);
		$sql .= '(' . implode(',', $quote_fields) . ')VALUES';
		$param = [];
		foreach ($values as $i => $value)
		{
			$sql .= '(:' . implode($i . ',:', $fields) . $i . '),';
			foreach ($fields as $k => $field)
			{
				$param[$field . $i] = $value[$k];
			}
		}
		$sql = rtrim($sql, ',');
		return $this->execute($sql, $param);
	}

	/**
	 * 删除数据
	 * @method delete
	 * @param  [string] $id [description]
	 * @return [array]     	[description]
	 * @author NewFuture
	 */
	public function delete($id = '')
	{
		if (null != $id)
		{
			if (is_array($id))
			{
				$this->data = array_merge($this->data, $id);
			}
			else
			{
				$this->data[$this->pk] = $id;
			}
		}
		$sql = 'DELETE ';
		$sql .= $this->buildFromSql();
		$where = $this->buildWhereSql();
		if (!$where)
		{
			return false;
		}
		else
		{
			$sql .= $where;
			return $this->execute($sql, $this->param);
		}
	}

	/**
	 * 数据读取操作
	 * @method query
	 * @param  [string] $sql  	[sql语句]
	 * @param  [type] $bind 	[description]
	 * @return [array]       	[结果数组]
	 * @author NewFuture
	 */
	public function query($sql, $bind = null)
	{
		$result = self::$_db->query($sql, $bind);
		$this->clear();
		return $result;
	}

	/**
	 * 数据写入修改操作
	 * @method execute
	 * @param  [string] $sql  	[sql语句]
	 * @param  [type] $bind 	[description]
	 * @return [array]       	[结果数组]
	 * @author NewFuture
	 */
	public function execute($sql, $bind = null)
	{
		$result = self::$_db->execute($sql, $bind);
		$this->clear();
		return $result;
	}

	/**
	 * 字段过滤
	 * @method field
	 * field('name','username')
	 * field('name AS username')
	 * field('id,name,pwd')
	 * field(['user.id'=>'uid'])
	 * @param  [string]		$field    	[字段]
	 * @param  [string] 	$alias 		[description]
	 * @return [object]        			[description]
	 * @author NewFuture
	 */
	public function field($field, $alias = null)
	{
		if ($alias && $field)
		{
			$this->fields[trim($field)] = trim($alias);
		}
		elseif(is_string($field))
		{
			/*多字段解析*/
			$fields = explode(',', $field);
			foreach ($fields as $field)
			{				
				/*解析是否为name AS alias的形式*/
				if ($pos=stripos($field, ' AS '))
				{
					$this->fields[substr($field,0,$pos)] = substr($field,$pos+4);
				}
				else
				{
					$this->fields[] = $field;
				}
			}
		}else
		{
			$this->fields=array_merge($this->fields,$field);
		}
		return $this;
	}

	/**
	 * where 查询
	 * @method where
	 * @param  [mixed] 	$key        [键值,条件数组,条件SQL]
	 * @param  [string] $exp        [description]
	 * @param  [mixed] 	$value      [description]
	 * @param  [string] $condition [逻辑条件]
	 * @return [object]             [description]
	 * @example
	 * where($data)
	 * where('id',1) 查询id=1
	 * where('id','>',1)
	 * @author NewFuture
	 */
	public function where($key, $op = null, $value = null)
	{
		$con = $this->parseWhere(func_get_args());
		$this->condition['and']=isset($this->condition['and'])?array_merge($this->condition['and'],$con):$con;
		return $this;
	}

	/**
	 * OR 条件
	 * @method orWhere
	 * @param  [mixed]  $key   	[键值,条件数组,条件SQL]
	 * @param  [string] $exp   	[description]
	 * @param  [string] $value 	[description]
	 * @return [type]         	[description]
	 * @author NewFuture
	 */
	public function orWhere($key, $op = null, $value = null)
	{
		$con = $this->parseWhere(func_get_args());
		$this->condition['or']=isset($this->condition['or'])?array_merge($this->condition['or'],$con):$con;
		return $this;
	}

	/**
	 * 排序条件
	 * @method order
	 * @param  [type]  		$fields     [description]
	 * @param  [boolean] 	$desc 		[是否降序]
	 * @return [array]              	[结果数组]
	 * @author NewFuture
	 */
	public function order($fields, $desc = false)
	{
		$this->order[trim($fields)] = ($desc === true || $desc === 1 || strtoupper($desc) == 'DESC') ? 'DESC' : '';
		return $this;
	}

	/**
	 * 限制位置和数量
	 * @method limit
	 * @param  integer $n      [description]
	 * @param  integer $offset [description]
	 * @return [type]          [description]
	 * @author NewFuture
	 */
	public function limit($n = 20, $offset = 0)
	{
		if ($offset > 0)
		{
			$this->limit = intval($offset) . ',' . intval($n);
		}
		else
		{
			$this->limit = intval($n);
		}
		return $this;
	}

	/**
	 * 翻页
	 * @method page
	 * @param  integer $p [页码]
	 * @param  integer $n [每页个数]
	 * @return [type]     [description]
	 * @author NewFuture
	 */
	public function page($p = 1, $n = 10)
	{
		return $this->limit($n, ($p - 1) * $n);
	}

	/**
	 * 统计
	 * @method count
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 * @author NewFuture
	 */
	public function count($field = null)
	{
		$exp = $field ? 'COUNT(' . self::qouteField($field) . ')' : 'COUNT(*)';
		$sql = $this->buildSelectSql($exp);
		$sql .= $this->buildFromSql();
		$sql .= $this->buildWhereSql();
		$result = self::$_db->single($sql, $this->param);
		$this->clear();
		return $result;
	}

	/**
	 * 表达式查询
	 * @method inc
	 * @param  [type]  $field [description]
	 * @param  [type]  $exp   [description]
	 * @param  integer $n     [description]
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	public function inc($field, $step = 1)
	{
		$sql = self::qouteField($field) . '=' . self::qouteField($field) . '+'.($this->setParam(intval($step)));
		return $this->update($sql);
	}

	/**
	 * @method __call
	 * MAX,MIN,AVG,SUM 数学计算
	 * @access private
	 * @author NewFuture
	 */
	public function __call($op, $args)
	{
		$op = strtoupper($op);
		if (in_array($op,Model::EXP ) && isset($args[0]))
		{
			//数学计算
			$sql = $this->buildSelectSql($op . '(' . self::backQoute($args[0]) . ')');
			$sql .= $this->buildFromSql();
			$sql .= $this->buildWhereSql();
			$result = self::$_db->single($sql, $this->param);
			$this->clear();
			return $result;
		}
		else
		{
			throw new Exception('不支持操作' . $op, 1);
		}
	}

	/**
	 * 设置字段值
	 * @method __set
	 * @param  [type]  $name  [description]
	 * @param  [type]  $value [description]
	 * @access protected
	 * @author NewFuture
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * 获取字段值
	 * @method __get
	 * @param  [type]  $name [description]
	 * @return [type]        [description]
	 * @access protected
	 * @author NewFuture
	 */
	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	public function __unset($name)
	{
		unset($this->data[$name]);
	}

	/**
	 * 设置数据
	 * @method set
	 * @param  [type] $data  [description]
	 * @param  [type] $value [description]
	 * @author NewFuture
	 */
	public function set($data, $value = null)
	{
		if (is_array($data))
		{
			$this->data = array_merge($this->data, $data);
		}
		else
		{
			$this->data[$data] = $value;
		}
		return $this;
	}

	/**
	 * 读取数据
	 * 如果有直接读取，无数据库读取
	 * @method get
	 * @param  [type] $name [字段名称]
	 * @param  [type] $auto_db [是否自动尝试从数据库获取]
	 * @return [type]       [description]
	 * @author NewFuture
	 */
	public function get($name = null, $auto_db = true)
	{
		if ($name)
		{
			if (isset($this->data[$name]))
			{
				return $this->data[$name];
			}
			elseif ($auto_db)
			{
				//数据库读取
				$sql = $this->buildSelectSql(self::backQoute($name));
				$sql .= $this->buildFromSql();
				$sql .= $this->buildWhereSql();
				$sql .= 'LIMIT 1';
				$data  = $this->data;
				$value = self::$_db->single($sql, $this->param);
				$this->clear();
				if ($value !== null)
				{
					$data[$name] = $value;
				}
				$this->data = $data;
				return $value;
			}
		}
		else
		{
			return empty($this->data) && $auto_db ? $this->find() : $this->data;
		}
	}

	/**
	 * 清空
	 * @method clear
	 * @return [type] [description]
	 * @author NewFuture
	 */
	public function clear()
	{
		$this->fields       = array(); //查询字段
		$this->group_fields = array();
		$this->data         = array(); //数据
		$this->condition    = array(); //查询条件
		$this->param        = array(); //查询参数
		$this->param_id     = 0; //参数ID归0
		$this->distinct     = false;   //是否去重
		$this->order        = array(); //排序字段
		$this->limit        = null;
		return $this;
	}

	/**
	 * @method group
	 * 分组
	 * @param  [type] $field        [description]
	 * @return [type] [description]
	 * @author NewFuture
	 */
	public function group($field)
	{
		$this->group_fields=array_merge($this->group_fields,$this->parseWhere(func_get_args()));		
		return $this;
	}

	/**
	 * field分析
	 * @access protected
	 * @param mixed $fields
	 * @return string
	 */
	protected function parseField()
	{
		$fields = $this->fields;
		
		$sql = '';
		if (empty($fields))
		{
			/*多表链接时加入表名*/
			// $sql =($this->belongs_to_tables||$this->has_tables) ? self::backQoute($this->table) . '.*' : '*';
			$sql =empty($this->join_tables) ?'*': self::backQoute($this->table) . '.*';
		}
		else
		{
			// 支持 'fieldname'=>'alias' 这样的字段别名定义
			foreach ($fields as $field => $alias)
			{
				if(is_int($field)||$field==$alias)
				{
					//无别名
					$sql.= self::qouteField($alias).',';
				}
				else
				{
					$sql .= self::qouteField($field) . 'AS' . self::backQoute($alias).',';
				}				
			}
			$sql=rtrim($sql,',');
		}
		return $sql;
	}

	/**
	 * 数据解析和拼接
	 * @method parseData
	 * @param  string    $pos [description]
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	protected function parseData($pos = ',')
	{
		$fieldsvals = array();
		foreach ($this->data as $column=>$value)
		{
			$fieldsvals[] = self::qouteField($column) . '=' .($this->setParam($value));
		}
		return implode($pos, $fieldsvals);
	}

	/**
	 * 构建select子句
	 * @method buildSelectSql
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	protected function buildSelectSql($exp = null)
	{
		$sql = $this->distinct ? 'SELECT DISTINCT ' : 'SELECT ';
		$sql .= $exp ?: $this->parseField();
		return $sql;
	}

	/**
	 * 构建From子句
	 * @method buildFromSql
	 * @return [type]       [description]
	 * @author NewFuture
	 */
	protected function buildFromSql()
	{
		// $this_table=self::backQoute($this->table);
		// $from = 'FROM' . $this_table . '';
		// //belong关系(属于)1，inner join
		// foreach ($this->belongs_to_tables as $fk => $table)
		// {
		// 	$from .= 'INNER JOIN' . self::backQoute($table) .
		// 	'ON' . self::qouteField($fk,$this->table) .
		// 	'=' . self::backQoute($table) . '.' . self::backQoute('id');
		// }
		// //has[其他表的外键指向此处] left join
		// foreach ($this->has_tables as $table => $fk) {
		// 	$from .= 'LEFT JOIN' .self::backQoute($table)
		// 	.'ON'.self::qouteField($this->pk,$this->table)
		// 	.'='.self::backQoute($table).'.'.self::backQoute($fk);
		// }
		
		$from = 'FROM' . self::backQoute($this->table);
		foreach ($this->join_tables as $join) {
			$from.=$join[0].' JOIN'.self::backQoute($join[1])
				.'ON'.$join[2].'='.$join[3];
		}
		return $from;
	}

	/**
	 * 构建where子句
	 * @method buildWhereSql
	 * @return [string]        [''或者WHERE(xxx)]
	 * @author NewFuture
	 */
	protected function buildWhereSql()
	{
		$pre   = $this->belongs_to_tables ? self::backQoute($this->table) . '.' : '';
		$sql   = empty($this->data) ? '' : '(' . $pre . $this->parseData(')AND(' . $pre) . ')';
		$where = $this->condition;

		if (!empty($where))
		{
			/*AND*/
			$and = isset($where['and']) ? $where['and'] : [];
			foreach ($and as $c)
			{
				//逐个and条件处理
				if (is_string($c))
				{
					//字符串形式sql
					$condition[] = $c;
				}
				else
				{
					//数组关系式
					list($key, $exp, $value) = $c;

					$condition[]  = self::qouteField($key) . $exp . $this->setParam($value);
				}
			}
			if (!empty($condition))
			{
				$and = '(' . implode(')AND(', $condition) . ')';
				$sql = $sql ? $sql . 'AND' . $and : $and;
			}

			/*OR*/
			$or = isset($where['or']) ? $where['or'] : [];
			unset($condition);
			foreach ($or as $c)
			{
				if (is_string($c))
				{
					$condition[] = $c;
				}
				else
				{
					list($key, $exp, $value) = $c;

					$condition[]  = self::qouteField($key). $exp .$this->setParam($value);
				}
			}
			if (!empty($condition))
			{
				$or  = '(' . implode(')OR(', $condition) . ')';
				$sql = $sql ? $sql . 'OR' . $or : $or;
			}
		}
		return $sql ? ' WHERE ' . $sql : '';
	}

	/**
	 * 构建尾部子句
	 * limt order等
	 * @method buildTailSql
	 * @return [type]       [description]
	 * @author NewFuture
	 */
	protected function buildTailSql()
	{
		$tail = '';

		/*GROUP BY*/
		foreach ($this->group_fields as $field) 
		{
			if(is_array($field))
			{
				$tail.='GROUP BY'.self::qouteField($field[0]).$field[1].$this->setParam($field[2]).' ';	
			}
			else
			{
				$tail.='GROUP BY'.self::qouteField($field);	
			}
		}

		/*ORDER BY*/
		if(!empty($this->order))
		{
			$tail.='ORDER BY';
			foreach ($this->order as $field => $value)
			{
				$tail .= self::qouteField($field) . $value.',';
			}
			$tail=rtrim($tail,',');
		}

		/*Limit*/
		if ($this->limit)
		{ 
			$tail .= ' LIMIT ' . $this->limit;
		}
		
		return $tail;
	}

	/**
	 * @method parseWhere
	 * @param  Array     $where        [数组参数]
	 * @return Array    格式化数组条件
	 * @author NewFuture
	 */
	protected function parseWhere($where)
	{
		$condition=array();
		switch (sizeof($where)) 
		{
			case 0://错误
				throw new Exception('非法where查询: NO argument in where' );
				break;

			case 1://纯SQL语句或者数组
				$key=$where[0];
				if (is_string($key))
				{
					//直接sql条件
					//TO 安全过滤
					$condition[] = $key;
				}
				elseif (is_array($key))
				{
					/*数组形式*/
					foreach ($key as $k => $v)
					{
						$condition[] = array($k, '=', $v);
					}
				}
				else
				{
					throw new Exception('非法where查询: UNKOWN TYPE' . json_encode($key));
				}
				break;

			case 2: //两个参数，相等
				$condition[] = [$where[0], '=', $where[1]];
				break;

			case 3://三参数表达式
				$op=$where[1];
				if(in_array(strtoupper($op),Model::OPERATOR['exp']))
				{
					$where[1]=$op.' ';
				}
				elseif(!in_array($op,Model::OPERATOR['sym']))
				{
					 throw new Exception('非法where查询: unknow operator=>' . $op.json_encode(func_get_arg()));
				}	
				$condition[]=$where;
			   	break;

			default:
			  throw new Exception('非法where查询: too many arguments=>' . json_encode(func_get_arg()));
				break;
		}
		return $condition;
	}

	/**
	 * @method setParam
	 * @param  [type]   $paramValue [description]
	 * @return 参数键值
	 * @author NewFuture
	 */
	protected function setParam($value)
	{
		$key=$this->param_id++;
		$this->param[$key] = $value;
		return ':'.$key;
	}

	/**
	 * @method qouteField
	 * 字段加引号
	 * @todo 复杂条件过滤
	 * @param  [type]     $field_str     [description]
	 * @param  [type]     $default_table [description]
	 * @return [type]     [description]
	 * @author NewFuture
	 */
	protected static function qouteField($field_str,$default_table=null)
	{
		if($pos=strpos($field_str,'('))
		{
			$op=strtoupper(substr($field_str,0,$pos));
			$field=rtrim(substr($field_str,$pos+1),')');
			if(strpos($field,' '))
			{
				//todo
				//解析过滤
				//复杂条件不解析
				return $op.'('.$field.')';
			}else
			{
				$field=explode('.',$field);
			}
		}
		else
		{
			$field=explode('.',$field_str);
		}
		
		switch(sizeof($field))
		{
			case 1:
				$qouted_sql= $default_table?self::backQoute($default_table).'.'.self::backQoute($field[0]):self::backQoute($field[0]);
				break;
			case 2:
				$qouted_sql=self::backQoute($field[0]).'.'.self::backQoute($field[1]);
				break;
			default:
				throw new Exception('Can not parseFieldwithTable[无法解析字段]:'.$field_str);
				
		}

		return isset($op)?$op.'('.$qouted_sql.')':$qouted_sql;
	}

	/**
	 * 对字段和表名进行反引字符串
	 * 并字符进行安全检查
	 * 合法字符为[a-zA-Z_]
	 * @method backQoute
	 * @param  [type]    $str [description]
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	public static function backQoute($str)
	{
		if (!ctype_alnum(strtr($str, '_', 'A')))
		{
			//合法字符为字母[a-zA-Z]或者下划线_
			throw new Exception('非法字符' . $str);
			die('数据库操作中断');
		}
		else
		{
			return '`' . $str . '`';
		}
	}

	/**json序列化接口实现**/
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**数组操作接口实现**/
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset, false);
	}

	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}
}
?>