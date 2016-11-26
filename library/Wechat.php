<?php
/**
 * 微信API接口封装
 * 在secret配置中[wechat]配置两个key即可
 */
class Wechat
{
	//企业号APItoken地址 $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
	const TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&';

	//企业号ticketAPI地址 $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
	const TICKET_URL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&';

	private static $_config;

	/**
	 * @method getConfig
	 * @param  配置Key    $key          [description]
	 * @return [type]    [description]
	 * @author NewFuture
	 */
	private static function getConfig($key)
	{
		if (!self::$_config)
		{
			self::$_config = Config::getSecret('wechat');
		}
		return self::$_config[$key];
	}

	/**
	 * @method Sign
	 * @param  [type]  $url          [签名的URL]
	 * @return [type]  [description]
	 * @author NewFuture
	 */
	public static function Sign($url)
	{
		$jsapiTicket = self::getJsApiTicket();
		$timestamp   = $_SERVER['REQUEST_TIME'];
		$nonceStr    = Random::word(16);
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string      = 'jsapi_ticket=' . $jsapiTicket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
		$signature   = sha1($string);
		$signPackage = array(
			'appId'     => self::getConfig('appid'),
			'nonceStr'  => $nonceStr,
			'timestamp' => $timestamp,
			'url'       => $url,
			'signature' => $signature,
			'rawString' => $string,
		);
		return $signPackage;
	}

	/**
	 * @method getJsApiTicket
	 * 获取JS API ticket
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	private static function getJsApiTicket()
	{
		$ticket = Cache::get('wx_api_jsticket');
		if (!$ticket)
		{
			$token = self::getAccessToken();
			$url   = self::TICKET_URL . 'access_token=' . $token;
			$res   = json_decode(self::httpGet($url), true);
			if ($res && isset($res['ticket']))
			{
				$ticket = $res['ticket'];
				Cache::set('wx_api_jsticket', $ticket, 6000);
			}
			else
			{
				throw new Exception('Wechat API get JS TICKET  Failed', 1);
			}
		}
		return $ticket;
	}

	/**
	 * @method getAccessToken
	 * 获取微信token
	 * @return [type]         [description]
	 * @author NewFuture
	 */
	private static function getAccessToken()
	{
		$token = Cache::get('wx_api_accesstoken');
		if (!$token)
		{
			$url = self::TOKEN_URL . 'appid=' . self::getConfig('appid') . '&secret=' . self::getConfig('secret');
			$res = json_decode(self::httpGet($url), true);
			if ($res && isset($res['access_token']))
			{
				$token = $res['access_token'];
				Cache::set('wx_api_accesstoken', $token, 6000);
			}
			else
			{
				throw new Exception('Wechat API get token Failed', 1);
			}
		}
		return $token;
	}

	private static function httpGet($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1000);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}
}
