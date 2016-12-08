<?php
	/*
	*学生评论
	*
	*/
	class EvaluationController extends Rest {
		
		public function init(){
			parent::init();
			$this->user = $this->getUser('user', true);
		}

		/*
		*@name 提交评论
		*@method POST
		*/
		public function POST_indexAction(){
			
			$user = $this->user;
			Input::post('cid', $course_id);
			Input::post('advice', $advice);
			Input::post('score1', $score1);
			Input::post('score2', $score2);
			Input::post('score3', $score3);
			Input::post('score4', $score4);
			Input::post('score5', $score5);
			Input::post('score6', $score6);
			Input::post('score7', $score7);
			Input::post('score8', $score8);
			Input::post('score9', $score9);
			Input::post('score10', $score10);
			Input::post('score11', $score11);
			Input::post('score12', $score12);
			Input::post('averge', $averge);

			$eval = [
				'course_id' => $course_id,
				'user_id' => $user['id'],
				'advice' => $advice,
				'score1' => $score1,
				'score2' => $score2,
				'score3' => $score3,
				'score4' => $score4,
				'score5' => $score5,
				'score6' => $score6,
				'score7' => $score7,
				'score8' => $score8,
				'score9' => $score9,
				'score10' => $score10,
				'score11' => $score11,
				'score12' => $score12,
				'averge' => $averge,
			];

			$evalOrm = Db::table('evaluation');

			if($id = $evalOrm->where('course_id', $course_id)->where('user_id', $user['id'])->get('id')){
				$this->response(0, 'already commented,,');
			}

			if($evalOrm->clear()->insert($eval)){
				$this->response(1, 'comment success,,');
			}else{
				$this->response(0, 'comment failed: database error,,');
			}

		}

		/*
		*GET /Evaluation/count
		*@name 获取该学生已评价课的数目
		*@method GET
		*
		*/
		public function GET_countAction(){
			$user = $this->user;
			$evalOrm = Db::table('evaluation');
			if($count = $evalOrm->where('user_id', $user['id'])->count('id')){
				$this->response(1, $count);
			}else{
				$this->response(0, 'query eval count failed: database error,,');
			}
		}

		/*
		*@name /Evaluation/:id
		**@method POST 获取评论详情
		*@param :id(评论id)		
		*/
		public function GET_infoAction($id){
			$user = $this->user;
			//up: 点赞数 down: 点踩数 vote：该user是否对 该eval点赞
			$evalvote = [
				'evaluation_id' => $id,
			];

			$evalvoteOrm = Db::table('evaluationvote');

			$up = $evalvoteOrm->where($evalvote)->where('vote', 1)->count();
			$down = $evalvoteOrm->clear()->where($evalvote)->where('vote', -1)->count();

			$evalvote['up'] = $up;
			$evalvote['down'] = $down;

			if($vote = $evalvoteOrm->clear()->where('evaluation_id', $id)->where('user_id', $user['id'])->get('vote')){
				$evalvote['vote'] = $vote;
			}

			$this->response(1, $evalvote);

		}

		/*
		*@name /Evaluation/:id/up
		**@method POST 对评论点赞
		*@param :id(评论id)		
		*/
		public function POST_upAction($id){

			if(!$id = intval($id)){
				$this->response(0, 'invalid eval id,,');
				return;
			}

			$user = $this->user;
			$evalvoteOrm = Db::table('evaluationvote');

			$evalvote = [
				'evaluation_id' => $id,
				'user_id' => $user['id'],
			];

			if($evalvoteRes = $evalvoteOrm->where($evalvote)->find()){
				if($evalvoteRes['vote'] == 1){
					//已 点赞
					$this->response(0, 'already up this eval,,');
				}else{
					if($evalvoteOrm->clear()->where($evalvote)->put('vote', 1)){
						$this->response(1, 'update eval up success,,');
					}else{
						$this->response(0, 'update eval up failed: database error,,');
					}
				}
				
			}else{
				$evalvote['vote'] = 1;
				if($res = $evalvoteOrm->clear()->set($evalvote)->add()){
					$this->response(1, 'up eval success,,');
				}else{
					$this->response(0, 'up eval failed: database error,,');
				}
			}
		}

		/*
		*@name /Evaluation/:id/down
		**@method POST 对评论点踩
		*@param :id(评论id)		
		*/
		public function POST_downAction($id){
			if(!$id = intval($id)){
				$this->response(0, 'invalid eval id,,');
				return;
			}

			$user = $this->user;
			$evalvoteOrm = Db::table('evaluationvote');

			$evalvote = [
				'evaluation_id' => $id,
				'user_id' => $user['id'],
			];

			if($evalvoteRes = $evalvoteOrm->where($evalvote)->find()){
				if($evalvoteRes['vote'] == -1){
					//已 点赞
					$this->response(0, 'already down this eval,,');
				}else{
					if($evalvoteOrm->clear()->where($evalvote)->put('vote', -1)){
						$this->response(1, 'update eval down success,,');
					}else{
						$this->response(0, 'update eval down failed: database error,,');
					}
				}
				
			}else{
				$evalvote['vote'] = -1;
				if($res = $evalvoteOrm->clear()->set($evalvote)->add()){
					$this->response(1, 'down eval success,,');
				}else{
					$this->response(0, 'down eval failed: database error,,');
				}
			}
		}






















	}