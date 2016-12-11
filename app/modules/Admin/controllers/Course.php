
<?php
/*
 *Techer接口
 *
 */
class CourseController extends Rest
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

		$CourseModel = new Model('course');
		$key         = strtok($keywords, ' ');
		while ($key)
		{
			$key = '%' . $key . '%';
			$CourseModel->orWhere('course.name', 'LIKE BINARY', $key);
			$key = strtok(' ');
		}
		$courses = $CourseModel
			->field([
				'course.id',
				'course.name',
				'course.number',
				'course.department_id',
			])
			->group('id')
			->page($page)
			->select();
		if (!empty($courses))
		{
			$this->response(1, $courses);
		}
		else
		{
			$this->response(0, null);
		}
	}

	/**
	 * @method POST_indexAction
	 * 查找教师
	 * @param name
	 * @author:zhaowenjian
	 */
	public function POST_indexAction()
	{

		$admin = $this->getUser('admin', true);

		if ($admin['privilege'] < 1)
		{
			$this->response(0, '无权设置权限');
		}
		elseif (Input::post('name', $name, 'trim') && Input::post('number', $number, 'trim'))
		{
			$course['name']   = $name;
			$course['number'] = $number;

			$CourseModel = new Model('course');
			if ($course_id = $CourseModel->add($course))
			{
				$this->response(1, $course_id);
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