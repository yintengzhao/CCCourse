<?php

	class AdviceController extends Rest{

		public function GET_indexAction(){

		}

		public function GET_hotAction(){

			$res = [
				[
					'advice' => " 有点感觉，还是有点憋，在自然点会更喜欢，加油",
					'id' => "28",
					'date' => "2016-06-06"
				],
				[
					'advice' => " 今年的夏天如此灼热,时间脚步匆匆往前，不留给人一点暇情。关于自身的渴望、心中的梦想，顶着阳光透彻深刻地思索，对于最后的答案终有些许眉目.......... ",
					'id' => "20",
					'date' => "2016-06-09"
				]
			];
			$this -> response( 1, $res);
		}



	}





?>