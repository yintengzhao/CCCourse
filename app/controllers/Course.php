<?php
/**
 * 课程信息
 */
class CourseController extends Rest
{
	
	public function init(){
		parent::init();
		$this->user = $this->getUser('user', true); 
	}

	/**
	 * @method GET_indexAction
	 * 课程查询
	 * 这一部分开放查询
	 * @param [page=1] 页码
	 * @param key 关键字
	 * @author NewFuture
	 * @todo 查询缓存
	 */
	public function GET_indexAction()
	{

		Input::get('key', $keywords, 'trim', '');
		Input::get('page', $page, 'intval', 1);

		//+_&相当于联合条件[注加号需要转义]
		//[空格],|相当于满足一条即可
		$keywords  = strtr($keywords, '+_&,|', '%%%  ');
		$cache_key = 'courses_' . $page . '_' . md5($keywords);
		if ($courses = Cache::get($cache_key))
		{
			/*直接返回缓存数据*/
			$this->response(1, $courses);
		}
		else
		{
			$courseOrm = Db::table('course');
			$courseOrm
				->has('evaluation')
				->field([
					'course.id',
					'course.number',
					'course.name_en',
					'course.name'          => 'course',
					'COUNT(evaluation.id)' => 'comments',
					'AVG(evaluation.averge)' => 'score',
				])
				->group('id')
				->page($page);

			if ($keywords)
			{
				$courseOrm->order('score', true)->order('comments', true);
				$key = strtok($keywords, ' ');
				while ($key)
				{
					$key = '%' . $key . '%';
					$courseOrm
						->orwhere('course.name', 'LIKE BINARY', $key);
					$key = strtok(' ');
				}
			}
			else
			{
				//无关键字时评论优先
				$courseOrm->order('comments', true)->order('score', true)->order('id', false);
			}

			$courses = $courseOrm->select();
			if (!empty($courses))
			{
				Cache::set($cache_key, $courses, Config::get('cache.expire'));
				$this->response(1, $courses);
			}
			else
			{
				$this->response(0, null);
			}

		}
	}

	/**
	 * @method GET_infoAction
	 * @param  [type]         $id [description]
	 * @author NewFuture
	 * @todo 详细信息
	 */
	public function GET_infoAction($id)
	{
		$user        = $this->getUser('user', True);
		$courseOrm = Db::table('course');
		$course = $courseOrm
			->has('evaluation')
			->field([
				'course.id',
				'course.name',
				'course.name_en',
				'course.description',
				'COUNT(evaluation.id)' => 'comments',
				'AVG(evaluation.averge)' => 'score',
			])
			->where('course.id',$id)
			->where('evaluation.status', 1)
			->group('id')
			->find();
		if ($course)
		{
			if ($user['type'] === 'student')
			{
				/*学生才有自己的评论*/
				$course['evaluation'] = (Db::table('evaluation'))
					->where(['course_id' => $id, 'user_id' => $user['id'], 'status' => 1])
					->field('id,course_id,user_id,advice,averge')
					->find();

				$this->response(1, $course);
			}else{
				$this->response(0, 'user is not stu,,');
			}
			
		}
		else
		{
			$this->response(0, '无此课程T_T');
		}
	}

	/**
	 * @method GET_comments
	 * 获取评论列表
	 * @param  int       $id [课程ID]
	 * @author NewFuture
	 */
	public function GET_commentsAction($id)
	{
		$user = $this->user;
		Input::get('page', $page, 'int', 1);
		$evalOrm = Db::table('evaluation');

		// 'COUNT(CASE WHEN advicevote.student_id=' . intval($this->user['id']) . ' THEN 1 ELSE NULL END)' => 'vote',
		$evaluations = $evalOrm
			->has('evaluationvote')
			->belongs('user')
			->where('course_id', intval($id))
			->where('evaluation.status', 1)
			->page($page)
			->safe(false)
			->field([
				'evaluation.id',
				'evaluation.advice', //评论
				'evaluation.averge',
				'evaluation.time',
				'user.number'                                   => 'number',
				'COUNT(CASE WHEN vote=1 THEN 1 ELSE NULL END)'  => 'up',
				'COUNT(CASE WHEN vote=-1 THEN 1 ELSE NULL END)' => 'down',
			])
			->group('id')
			->order('up', true)
			->order('id', true)
			->select();
		if (!empty($evaluations) && $user['type'] === 'student')
		{
			/*学生才显示自己是否可以赞踩*/
			$voteOrm = Db::table('evaluationvote');
			foreach ($evaluations as &$e)
			{
				$e['vote'] = $voteOrm
					->where('evaluation_id', $e['id'])
					->where('user_id', $user['id'])
					->get('vote');
				$voteOrm->clear();
			}
		}
		$this->response(1, $evaluations);
	}

}
?>
