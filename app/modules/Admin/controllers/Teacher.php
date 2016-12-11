
<?php
/*
 *Techer接口
 *
 */
class TeacherController extends Rest
{
	/**
	 * @method GET_indexAction
	 * 查找教师
	 * @param key 关键字
	 * @param page=1 页码
	 * @author:zhaowenjian
	 */

	public function GET_indexAction()
	{
		$admin = $this->getUser('admin', true);

		Input::get('key', $keywords, 'trim', '');
		Input::get('page', $page, 'intval', 1);

		$keywords = strtr($keywords, '+_&,|', '%%%  ');

		$TeacherModel = new Model('teacher');
		$key          = strtok($keywords, ' ');
		while ($key)
		{
			$key = '%' . $key . '%';
			$TeacherModel->orWhere('teacher.name', 'LIKE BINARY', $key);
			$key = strtok(' ');
		}
		$teacheres = $TeacherModel
			->field([
				'teacher.id',
				'teacher.name',
				'teacher.number',
				'teacher.time',
				'teacher.department_id',
			])
			->group('id')
			->page($page)
			->select();
		if (!empty($teacheres))
		{
			$this->response(1, $teacheres);
		}
		else
		{
			$this->response(0, null);
		}
	}

	/**
	 * @method POST_indexAction
	 * 添加教师
	 * @param name
	 * @param number
	 * @author:zhaowenjian
	 */

	public function POST_indexAction()
	{
		$admin = $this->getUser('admin', true);
		
		if ($admin['privilege'] < 1)
		{
			$this->response(0, '无权添加');
		}
		elseif (Input::post('name', $name, 'trim') && Input::post('number', $number, 'trim'))
		{
			$teacher = [
				'name'   => $name,
				'number' => $number,
			];
			$TeacherModel = new Model('teacher');
			if ($teacher_id = $TeacherModel->add($teacher))
			{
				$this->response(1, $teacher_id);
			}
			else
			{
				$this->response(0, '添加失败！');
			}
		}
		else
		{
			$this->response(0, '所填信息有误！');
		}
	}
}

?>