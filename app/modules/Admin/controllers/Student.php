
<?php
/*
 *Student
 *
 */
class StudentController extends Rest
{

	/**
	 * @method init
	 * 检查登录情况
	 * @todo 多级权限
	 * @author ZhaoWenjian
	 */
	protected function init()
	{
		$this->admin = $this->getUser('admin', true);
		//权限判断
		parent::init();
	}


	/**
	 * @method GET_evaluationAction
	 * 查找教师
	 * @param key 关键字
	 * @param page=1 页码
	 * @author NewFuture
	 */
	public function GET_evaluationAction()
	{
		if (Input::get('number', $number,'ctype_digit'))
		{
			$UserModel = new Model('user');
			$result    = $UserModel
				->has('evaluation', 'user_id')
				->belongs('class', 'evaluation.class_id')
				->belongs('teacher', 'class.teacher_id')
				->belongs('course', 'class.course_id')
				->where('user.number', $number)
				->field([
					'user.id',
					'user.number',
					'user.name',
					'teacher.name'  => 'teacher',
					'course.name'   => 'course',
					'evaluation.id' => 'eid',
					'evaluation.time',
					'evaluation.class_id',
					'evaluation.rank',
					'evaluation.comment',
					'evaluation.status',
				])->select();
			$this->response(1, $result);
		}else{
			$this->response(0,'无学号');
		}
	}

	public function GET_adviceAction()
	{
		if (Input::get('number', $number, 'ctype_digit'))
		{
			$UserModel = new Model('user');
			$result    = $UserModel
				->has('advice', 'user_id')
				->where('user.number', $number)
				->field([
					'user.id',
					'user.number',
					'user.name',
					'advice.id' => 'aid',
					'advice.time',
					'advice.content',
					'advice.status',
					'advice.status',
					'advice.useful',
					'advice.technology',
					'advice.art',
					'humanity',
					'social',
					'practice',
					'other',
				])->select();
			$this->response(1, $result);
		}else{
			$this->response(0,'无学号');
		}
	}
}

?>