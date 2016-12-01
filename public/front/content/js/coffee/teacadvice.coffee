
@ng_app.controller("TeacadviceCtrl", ['$controller',  '$scope', '$location', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $location, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	$scope.page_state = 1
	$scope.advice = {}
	$scope.contact = {}

	$scope.eval_items = [
		{
			id: 1,
			score: 2,
			content: "符合国务院学位委员会对学位申请人的基本要求"
		},
		{
			id: 2,
			score: 2,
			content: "能够代表学位申请人在理论知识方面应达到的广度和深度"
		},
		{
			id: 3,
			score: 2,
			content: "能够培养学位申请人在行业内具有一定的实践能力"
		},
		{
			id: 4,
			score: 2,
			content: "注意区分同一学科下不同层次的培养方案，注重层次衔接"
		},
		{
			id: 5,
			score: 2,
			content: "必修课基本覆盖了本学科主要基础知识"
		},
		{
			id: 6,
			score: 2,
			content: "选修课是否包含学科前沿知识"
		},
		{
			id: 7,
			score: 2,
			content: "选修课是否包含提升学生实践能力的课程"
		},
		{
			id: 8,
			score: 2,
			content: "每门课程均有授课内容的不同侧重点"
		},
		{
			id: 9,
			score: 2,
			content: "培养方案能够代表一定的南开特色"
		}
	]
	
	scheme_id = $location.search().id
	console.log scheme_id
	$scope.submit_handler = ->
		form =  $scope.advice_form
		params = {
			type: scheme_id
			scores: []
			advice: ""
		}
		for i in $scope.range(9)
			s = {
				id: $scope.eval_items[i].id
				score: $scope.eval_items[i].score
			}
			s.score += s.score%2
			s.score = if s.score>1 then s.score else 2
			params.scores.push s

		params.advice = $scope.advice.content
		form.$setSubmitted()
		
		if form.$invalid
			$scope.popup_toast()
			return
		
		$scope.submiting = true
		NetManager.post("/Scheme", params).then (data)->
			if +data.status != 1
				$scope.popup_toast(data.info)
			else
				$window.location = "./advice_feedback.html"

	$scope.on_advice_page = ->
		$scope.page_state == 1

	$scope.user_info_skip = ->
		$window.location = "./advice_feedback.html"


])