<?php
	/*
		教师评测数据提交接口
	*/
	class AdviceController extends Rest{

		/*
			*@init
			*登录检查
		*/
		protected function init(){
			parent::init();
			$this->user = $this->getUser('user', true);
		}

		/*
			*@POST /Advice/:id
			*点赞 id(advice id)
			*@param
		*/

		public function GET_infoAction($id){

			if(!$advice_id = intval($id)){
				$this->response(0, 'error advice id');
			}else{
				if(
					$advice = Db::table('advice')
						->where('id', $advice_id)
						->field('user_id, advice, status, time')
						->find()
				){
					$advice['up'] = Db::table('advicevote')
						->where('advice_id', $advice_id)
						->where('vote', 1)
						->count();
					$advice['down'] = Db::table('advicevote')
						->where('advice_id', $advice_id)
						->where('vote', -1)
						->count();
					$advice['vote'] = Db::table('advicevote')
						->where('advice_id', $advice_id)
						->where('user_id', $this->user['id'])
						->get('vote');
					$this->response(1, $advice);
				}else{
					$this->response(0, 'advice not exist');
				}
			}

		}

		/*
			*@POST /Advice/:id/up
			*点赞 id(advice id)
			*@param
		*/
		public function POST_upAction($id){
			$user = $this->user;
			$vote = [
				'user_id' => (string)($this->user['id']),
				'advice_id' => (string)($id),
			];
			//var_dump($vote);
			if(
				$res = Db::table('advicevote')
					//->where($vote)
					->where('user_id', $user['id'])
					->where('advice_id', $id)
					->field('vote')
					->find()
			){
				//已经vote过了
				if($res['vote'] == 1){
					//以前up过
					$this->response(0, 'already uped for this advice');
				}else{
					//以前down过
					if(
						Db::table('advicevote')
							->where('user_id', $user['id'])
							->where('advice_id', $id)
							->set('vote', 1)
							->save()
					){
						$this->response(1, "update advice up success");
					}else{
						$this->response(0, "update advice up failed: database error");
					}
				}

			}else{
				$vote['vote'] = 1;
				if(
					Db::table('advicevote')
						->insert($vote)
				){
					$this->response(1, "post advice up success");
				}else{
					$this->response(0, "post advice up failed: database error");
				}

			}

		}

		/*
			*@POST /Advice/:id/
			*点踩 id(teac_advice id)
			*@param
		*/
		public function POST_downAction($id){
			$user = $this->user;
			$vote = [
				'user_id' => (string)($this->user['id']),
				'advice_id' => (string)($id),
			];
			//var_dump($vote);
			if(
				$res = Db::table('advicevote')
					//->where($vote)
					->where('user_id', $user['id'])
					->where('advice_id', $id)
					->field('vote')
					->find()
			){
				
				if($res['vote'] == -1){
					$this->response(0, 'already downed for this advice');
				}else{
					if(
						Db::table('advicevote')
							->where('user_id', $user['id'])
							->where('advice_id', $id)
							->set('vote', -1)
							->save()
					){
						$this->response(1, "update teac_advice down success");
					}else{
						$this->response(0, "update teac_advice down failed: database error");
					}
				}

			}else{
				$vote['vote'] = -1;
				if(
					Db::table('advicevote')
						->insert($vote)
				){
					$this->response(1, "post teac_advice down success");
				}else{
					$this->response(0, "post teac_advice down failed: database error");
				}
			}

		}

	}





?>