<?PHP

class CourseController extends Rest{
	
	//get Courses: computer control
	//Get /Course/computer

	public function GET_indexAction(){
		Input::get('type', $type);

		if($type == 'computer'){
			$res = [
				[
					"course" => "computer-当思念飞过夜空",
					"comments" => "27",
					"id" => "1000"
				],
				[
					"course" => "computer-缠绕指尖停留",
					"comments" => "28",
					"id" => "1001"
				],
				[
					"course" => "computer-只剩下一场梦",
					"comments" => "29",
					"id" => "1002"
				],
				[
					"course" => "computer-在你背影转身后",
					"comments" => "30",
					"id" => "1002"
				],
				[
					"course" => "computer-吞噬在人海中",
					"comments" => "31",
					"id" => "1003"
				],
				[
					"course" => "computer-擦不干的寂寞",
					"comments" => "32",
					"id" => "1004"
				],
				[
					"course" => "computer-never try never say",
					"comments" => "33",
					"id" => "1005"
				]
			];
		} elseif($type == 'control'){
			$res = [
				[
					"course" => "control-当思念飞过夜空",
					"comments" => "27",
					"id" => "1000"
				],
				[
					"course" => "control-缠绕指尖停留",
					"comments" => "28",
					"id" => "1001"
				],
				[
					"course" => "control-只剩下一场梦",
					"comments" => "29",
					"id" => "1002"
				],
				[
					"course" => "control-在你背影转身后",
					"comments" => "30",
					"id" => "1002"
				],
				[
					"course" => "control-吞噬在人海中",
					"comments" => "31",
					"id" => "1003"
				],
				[
					"course" => "control-擦不干的寂寞",
					"comments" => "32",
					"id" => "1004"
				],
				[
					"course" => "control-never try never say",
					"comments" => "33",
					"id" => "1005"
				]
			];
		}

		//$this->response = ['type' => $type, 'method' => 'GET'];
		$this->response(1, $res);
	}

	//get hotCourse
	public function GET_hotAction(){

		Input::get('type', $type);
		
		$res = [
			[
				"course" => "当思念飞过夜空",
				"comments" => "28"
			],
			[
				"course" => "当思念飞过夜空",
				"comments" => "28"
			]
		];
		//$this->response = ['type' => $type, 'method' => 'GET'];
		$this->response(1, $res);
	}

	public function GET_infoAction($id){
		if(intval($id) == 1000){
			$res = [
				"id" => $id,
				"name" => "computer-当思念飞过夜空",
				"score" => 5,
				"description" => "是落櫻繽紛時候,一縷愁悵掠過 ,我記得那感受 ,那麼傷 那麼怨 那麼那麼痛 ,那麼愛 那麼恨 那麼那麼濃..."
			];
			$this -> response(1, $res);
		}else{
			$res = [
				"id" => $id,
				"name" => "control-在你背影转身后",
				"score" => 5,
				"description" => "誰的愛 太瘋 任性的 揮霍  每場 爭執 合好之後  我們 擁抱 狂吻陷落  誰的愛 不瘋 不配談 愛過  不求 明天 永恆 以後  眼神 燃燒 此刻 有我，就足夠 ..."
			];
			$this -> response(1, $res);
		}
	}

	public function GET_commentsAction($id){
		if(intval($id) == 1000){
			$res = [
				[
					"number" => "c100000",
					"content" => "「白子畫，我以神的名義詛咒你，今生今世，永生永世，不老不死，不傷不滅。白子畫，今生我從未後悔過，可是，若能重來一次，我再也不要愛上你。」",
					"score" => "4"
				],
				[
					"number" => "c100001",
					"content" => "一方面她也在賭，從前白子畫最在乎的是天下是長留，而今花千骨是妖神不容於世，一方面也有怨吧! 明明就是愛的,卻不肯承認.....愛也好 怨也罷 背負的太多 到後來.....只是累了﻿",
					"score" => "3"
				]
			];
			$this -> response(1, $res);
		}
	}
}











?>