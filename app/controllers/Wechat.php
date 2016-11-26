<?php
/**
 * 建议接口
 */
class WechatController extends Rest
{
	/**
	 * @method init
	 * 账号登陆检查
	 * @author NewFuture
	 */
	// protected function init()
	// {
	// 	parent::init();
	// 	$this->user = $this->getUser('user', true);
	// }

	/**
	 * @method GET_tokenAction
	 * 获取建议列表
	 * @param [page=1] 页码
	 * @author NewFuture
	 */
	public function GET_tokenAction()
	{
		if (!Input::get('url', $url))
		{
			$url = $this->_request->getServer('HTTP_REFERER') ?: $this->_request->getServer('HTTP_ORIGIN');
		}

		if ($signature = Wechat::Sign($url))
		{
			unset($signature['rawString']);
			$this->response(1, $signature);
		}
		else
		{
			$this->response(0, null);
		}
	}
}
?>