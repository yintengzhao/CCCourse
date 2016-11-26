<?php

/*
	用户接口
 */

class UserController extends Rest
{

 	/*
 		@method GET_indexAction
 		获取当前用户状态和信息
 	*/

 	public function GET_indexAction()
    {
    	$user = $this->getUser('user', true);
    	$this->response(1, $user);
    }

    /*
    	@method POST_indexAction
    	获取当前用户状态和信息
    */

    public function POST_indexAction(){
    	

    	Input::post('type', $type);

    	if( $type === 0){
    		//学生登录

    		if( Input::post('number', $number, 'ctype_digit') && Input::post('name', $name) ){

    			$info = ['number' => $number, 'name' => $name];
    			$UserModel = new Model('user');

    			if( $user = $UserModel->where($info)->field('id, type, status')->find() ){
    				//用户存在
    				//$user {id:, status, type, }

    				$UserModel->update(['lastlogin' => date('Y-m-d H:i:s')]);

    				if($user['status'] < 1){
    					$this->response(0, '该账号已屏蔽');
    					return;
    				}

    				if($user){

    					$user['token'] = $this->setUser('user', $user);
    					$this->response(1, $user);

    				}

    			}else{
    				$this->response(0, '账号密码不匹配');
    			}

    		}else{

	   			$this->response(0, '账号或者密码格式错误');

    		}

    	}elseif($type === 1){
    		//教师登录

    		$number = Random::number(6);
    		$type = Random::char(6);

    		$user = ['id' => $number, 'type' => $type, 'status' => 1];

    		$user['token'] = $this->setUser('user', $user);
    		$this->response(1, $user);

    	}

    }
	
	/*
    @method POST_logoutAction
    注销
    */

    public function POST_logoutAction(){
    	Cookie::flush();
    	$this->response(1, '注销成功');
    }

}

