
<?php
/*
 *Class接口
 *
 */
class ClassController extends Rest
{
	/**
	 * @method POST_indexAction
	 * 添加Class
	 * @param teacher_id
	 * @param course_id
	 * @author:zhaowenjian
	 */
	public function POST_indexAction()
	{
		$admin = $this->getUser('admin', true);

		if ($admin['privilege'] < 1)
		{
			$this->response(0, '无权添加');
		}
		elseif (Input::post('teacher_id', $teacher_id, 'intval') && Input::post('course_id', $course_id, 'intval'))
		{
			$class = [
				'teacher_id' => $teacher_id,
				'course_id'  => $course_id,
			];
			$ClassModel = new Model('class');
			if ($class_id = $ClassModel->add($class))
			{
				$this->response(1, $class_id);
			}
			else
			{
				$this->response(0, '已存在，请核对后重试！');
			}
		}
		else
		{
			$this->response(0, '所填信息有误！');
		}

	}

}

?>