<?php
	/*
		教师评测数据提交接口
	*/
	class SchemeController extends Rest{

		/*
			*@init
			*登录检查
		*/
		protected function init(){
			parent::init();
			$this->user = $this->getUser('user', true);
		}

		/*
			*@POST /Scheme/
			*教师提交评测数据
			*@param form_data
		*/
		public function POST_indexAction(){
			$user = $this->user;
			
			if(Input::post('scores', $scores) && Input::post('advice', $advice) && Input::post('type', $type)){
				$advice = [
					"advice" => $advice,
					"user_id" => $user["id"]
				];
				$sum = 0;
				foreach ($scores as $key => $value) {
					# code...
					$sum += intval($value["score"]);
					$advice["score".$value["id"]] = $value["score"];
				}
				$advice["averge"] =  floatval($sum / 9);
				$advice["type"] = $type;
				/*var_dump($advice)
				$test_advice = [
					"type" => 1,
					"user_id" => 123456
				];*/

				if(Db::table('advice')->insert($advice)){
					$this->response(1, "insert advice success");	
				}else{
					$this->response(0, 'insert advice failed: database error');
				}
				
			}else{
				$this->response(0, 'params error');
			}
		}

		/*
			*@GET /Advice/:id/advices
			*获取热门评论 id(培养方案类型)
			*@param page
		*/
		public function GET_advicesAction($id){

			Input::get('page', $page, 'int', 1);
			Input::get('order', $order, function(&$order){
				return ($order && strtolower($order) == 'new') ? 'id' : 'hot';
			}, 'hot');//排序方式

			$cache_key = 'advices_'.$id.$order.$page;
			//查找缓存
			if(!$advices = Cache::get($cache_key)){
				$advices = Db::table('advice')
					->has('advicevote')
					->field([
						'advice.id',
						'advice.advice',
						'advice.time',
						'advice.user_id',
						'advice.averge',
						'SUM(advicevote.vote)'=>'hot',
						/*'COUNT(CASE WHEN advicevote.vote=1 THEN 1 ELSE NULL END)'  => 'up',*/
					])
					->where('type', $id)
					->where('advice.status', 1)
					->group('id')
					->order($order, true)
					->page($page)
					->select();

				foreach ($advices as $key => $value) {
					# code...
					$advice_id = $value['id'];
					/*$up = [
						'advice_id' => $id,
						'vote' => 1,
					];*/
					$upcnt = Db::table('advicevote')
								->where('advice_id', $advice_id)
								->where('vote', 1)
								->count();

					$downcnt = Db::table('advicevote')
								->where('advice_id', $advice_id)
								->where('vote', -1)
								->count();

					$advices[$key]['up'] = $upcnt;
					$advices[$key]['down'] = $downcnt;
				}
				if(!empty($advices)){
					Cache::set($cache_key, $advices, Config::get('cache.expire'));
				}
			}
			if(empty($advices)){
				$this->response(0, 'null');
			}else{
				$user = $this->user;
				foreach ($advices as &$adv) {
					# code...
					$adv['vote'] = Db::table('advicevote')
						->where('advice_id', $adv['id'])
						->where('user_id', $user['id'])
						->get('vote');

				}
			}

			$this->response(1, $advices);

		}

		/*
			*@GET /Advice/:id
			*获取培养方案平均分 id(培养方案类型)
			*@param
		*/
		public function GET_infoAction($id){

			if(
				$score = Db::table('advice')
							->where('type', $id)
							->field([
								'AVG(advice.averge)' => 'score'
							])
							->find()
			){
				$this->response(1, $score);
			}else{
				$this->response(0, "get advice averge score failed: database error");
			}

		}

	}





?>