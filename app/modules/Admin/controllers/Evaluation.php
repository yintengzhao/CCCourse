<?php
/**
 * Adive 控制器
 */
class EvaluationController extends Rest
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
	 * @method GET_indexAction
	 * 管理员获得未审核Advice
	 * @author ZhaoWenjian
	 */
	public function GET_indexAction()
	{

		Input::get('page', $page, 'int', 1); //页码
		Input::get('type', $type, 'int', 0); //页码

		$EvalOrm = Db::table('evaluation');
		$evals     = $EvalOrm
			->field([
				'evaluation.id',
				'evaluation.time',
				'evaluation.status',
			])
			->where('evaluation.status', '=', $type)
			->group('id')
			->page($page)
			->select();

		$this->response(1, $evals);

	}

	/**
	 * @method POST_checkAction
	 * /Admin/Evaluation/$id?status=1
	 * 管理员修改密码
	 * @author ZhaoWenjian
	 */
	public function POST_checkAction($id)
	{

		$EvalOrm = Db::table('evaluation');

		if (Input::post('status', $status, 'int'))
		{

			$status = $status > 0 ? 1 : -1;
			if ($EvalOrm->where('id', $id)->set('status', $status)->set('admin_id', $this->admin['id'])->save())
			{

				$this->response(1, $status);

			}
			else
			{
				$this->response(0, '审核失败，，');
			}

		}
	}

}
?>
