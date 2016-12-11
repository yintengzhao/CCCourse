<?php
/**
 * Admin 控制器
 */
class AdminController extends Rest
{

	public function indexAction()
	{
		if (Config::get('debug') && Input::get('account', $account, 'account') && Input::get('password', $password, 'trim'))
		{
			echo Encrypt::encryptPwd(md5($password), $account);
		}
		else
		{
			$this->redirect('/admin/');
		}
	}

	/**
	 * @method POST_indexAction
	 * 添加账号
	 * @author NewFuture
	 */
	public function POST_indexAction()
	{
		$admin        = $this->getUser('admin', true);
		$AdminOrm   = Db::table('admin');
		$adminAccount = $AdminOrm->where('id', $admin['id'])->get('account');

		if ($admin['privilege'] < 1)
		{
			$this->response(0, '无权设置账号');
		}
		elseif (!Input::post('currentPwd', $currentPwd, 'trim'))
		{
			$this->response(0, '原始密码格式错误！');
		}
		elseif (!Input::post('account', $account, 'account'))
		{
			$this->response(0, '账号格式错误');
		}
		elseif(!Input::post('password', $password, 'trim'))
		{
			$this->response(0, '密码格式错误');
		}
		else
		{
			$_currentPwd = Encrypt::encryptPwd(md5($currentPwd), $adminAccount);

			$_admin = $AdminOrm
				->where('account', $adminAccount)
				->where('password', $_currentPwd)
				->field('id,name,privilege')
				->find();
 
			if ($_admin)
			{
				$password   = Encrypt::encryptPwd(md5($password), $account);
				$AdminOrm = Db::table('admin');

				$_admin = ['account' => $account, 'password' => $password, 'privilege' => 0];
				if ($aid = $AdminOrm->add($_admin))
				{
					$this->response(1, $aid);
				}
				else
				{
					$this->response(0, '添加出错');
				}
			}
			else
			{
				$this->response(0, '当前用户密码错误！');
			}
		}
	}

	/**
	 * @method PUT_passwordAction
	 * 管理员修改密码
	 * @author NewFuture
	 */
	public function PUT_passwordAction()
	{
		$admin = $this->getUser('admin', true);

		$AdminOrm = Db::table('admin');
		$account    = $AdminOrm->where('id', $admin['id'])->get('account');

		if (!Input::put('currentPwd', $currentPwd, 'trim'))
		{
			$this->response(0, '原始密码格式错误！');
		}
		elseif (!Input::put('password', $password, 'trim'))
		{
			$this->response(0, '密码格式错误！');
		}
		else
		{

			$currentPwd = Encrypt::encryptPwd(md5($currentPwd), $account);

			$admin = $AdminOrm
				->where('account', $account)
				->where('password', $currentPwd)
				->field('id,name,privilege')
				->find();

			if ($admin)
			{
				$password = Encrypt::encryptPwd(md5($password), $account);

				if ($AdminOrm
					->where('account', $account)
					->set('password', $password)
					->save())
				{
					$this->response(1, '修改成功!');
				}
				else
				{
					$this->response(0, '修改失败！');
				}

			}
			else
			{
				$this->response(0, '原始密码错误！');
			}

		}
	}

	/**
	 * @method POST_loginAction
	 * 管理员登录
	 * POST /Admin/Admin/login
	 * @author NewFuture
	 */
	public function POST_loginAction()
	{
		if (!Input::post('account', $account, 'account') || !Input::post('password', $password, 'trim'))
		{
			$this->response(0, '账号密码格式错误');
		}
		elseif (!Safe::checkTry('admin_' . $account, 5))
		{
			$this->response(0, '错误尝试次数太多,已禁止登录,请12小时后登录或联系管理人员');
		}
		else
		{
			$password   = Encrypt::encryptPwd(md5($password), $account);
			$AdminOrm = Db::table('admin');
			$admin      = $AdminOrm
				->where('account', $account)
				->where('password', $password)
				->field('id,name,privilege')
				->find();
			if ($admin)
			{
				Logger::write('administrator[' . $account . '] login succeed@' . Safe::IP());
				Session::get('admin', $admin);
				Safe::del('admin_' . $account);
				$info = [
					'id'        => $admin['id'],
					'privilege' => $admin['privilege'],
					'name'      => $admin['name'],
				];
				$this->setUser('admin', $info);
				$this->response(1, $info);
			}
			else
			{
				$this->response(0, '密码错误！');
			}
		}
	}

	/**
	 * @method POST_logoutAction
	 * 注销
	 * @author ZhaoWenjian
	 */
	public function POST_logoutAction()
	{

		Session::del('admin');

		$this->response(1, '已注销！');

	}
}
?>
