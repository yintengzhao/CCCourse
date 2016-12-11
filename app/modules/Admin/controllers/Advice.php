<?php
/**
 * Adive 控制器
 */
class AdviceController extends Rest
{
	/**
	 * @method init
	 * 检查登录情况
	 * @todo 多级权限
	 * @author NewFuture
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
		Input::get('type', $type, 'int', 0);

		$AdvcieOrm = Db::table('advice');
		$advices     = $AdvcieOrm
			->field([
				'advice.id',
				'advice.time',
				'advice.status',
			])
			->where('advice.status', '=', $type)
			->group('id')
			->page($page)
			->select();

		$this->response(1, $advices);

	}

	/**
	 * @method POST_checkAction
	 * /Admin/$id?status=1
	 * 管理员修改密码
	 * @author ZhaoWenjian
	 */
	public function POST_checkAction($id)
	{

		$AdvcieOrm = Db::table('advice');

		if (Input::post('status', $status, 'int'))
		{

			$status = $status > 0 ? 1 : -1;
			if ($AdvcieOrm->where('id', $id)->set('status', $status)->set('admin_id', $this->admin['id'])->save())
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
