@courseCtrl = angular.module('courseController', []);


@courseCtrl.controller("CourseCtrl", ['$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	cid =  $location.search().cid
	current_page = 1

	NetManager.get("/Course/#{cid}").then (data)->
		$scope.course = data.info

	$scope.switching = false 
	$scope.comment = {}
	$scope.questions = [
		{
			options: [
					"感兴趣，有需要"
					"讲的好"
					"给分高"
					"容易过"
					"随便选的"
					"其他"
				]
			anwser: []
			other: null
		}
	]

	$scope.comment_items = [
		{
			id: 1,
			score: 2,
			content: "课程大纲知识体系完整，内容丰富"
		},
		{
			id: 2,
			score: 2,
			content: "教学目标清晰，明确"
		},
		{
			id: 3,
			score: 2,
			content: "教学内容结构合理，难易适度，详略得当"
		},
		{
			id: 4,
			score: 2,
			content: "教学案例实用、典型、新颖，可操作性强"
		},
		{
			id: 5,
			score: 2,
			content: "教学内容能反映或联系学科发展的新思想，新概念，新成果"
		},
		{
			id: 6,
			score: 2,
			content: "课程要求教师具有较高的学术水平"
		},
		{
			id: 7,
			score: 2,
			content: "课程要求教师具有较高的实践经验"
		},
		{
			id: 8,
			score: 2,
			content: "备课充分，授课熟练，语言表达流畅，富有感染力"
		},
		{
			id: 9,
			score: 2,
			content: "及时答疑，耐心辅导"
		},
		{
			id: 10,
			score: 2,
			content: "通过课程，有利于对专业知识领域的掌握"
		},
		{
			id: 11,
			score: 2,
			content: "通过课程，可促进本行业的实践能力"
		},
		{
			id: 12,
			score: 2,
			content: "通过课程，可引导学生拓展知识面，提高自学能力"
		}
	]

	
	NetManager.get("/Course/#{cid}/comments").then (data)->
		console.log data
		# $scope.comments = data.info		
		$scope.comments = data.info
		if $scope.comments.length < 10
			$scope.no_more = true


	$scope.vote = (c, action)->
		if action!="up" && action!="down" then console.log("wrong action!"); return
		unless c[action]
			net_action = NetManager.post
		else
			net_action = NetManager.delete
		NetManager.post("/Evaluation/#{c.id}/#{action}").then (data)->
			console.log data
			if +data.status == 1
				NetManager.get("/Evaluation/#{c.id}").then (data)->
					return if +data.status!=1
					info = data.info
					c.up = info.up
					c.down = info.down
					c.vote = info.vote

	$scope.comment_handler = ->
		# NTBI
		$('body').scrollTop(0)
		$scope.page_state = 2
		$scope.switching = true

	$scope.back_handler = ->
		if $scope.switching
			$('body').scrollTop(0)
			$scope.switching = false
		else
			if $window.history.length > 1
				$window.history.back()
			else
				$window.location.href = "./index.html"


	$scope.comment_submit = ()->
		c = $scope.comment
		c.score += c.score%2
		c.score = if c.score>1 then c.score else 2
		params = {
			cid: cid,
			advice: c.content
		}
		sum = 0
		for i in $scope.range(12)
			c = $scope.comment_items[i]
			c.score += c.score%2
			c.score = if c.score>1 then c.score else 2
			sum += c.score
			params['score' + (i + 1)] = c.score
		params['averge'] = sum / 12
		console.log params
		$scope.submiting = true
		NetManager.post("/Evaluation", params).then (data)->
			if +data.status==1
				$window.location = "./comment_result.html"
			else
				console.log data
				$scope.submiting = false
				$scope.error_msg = data.info

	$scope.load_more = ->
		current_page += 1
		request_parm = {page: current_page}
		NetManager.get("/Course/#{cid}/comments", request_parm).then (data)->
			# $scope.searching = false
			console.log data
			course_ar = data.info
			if course_ar.length == 0
				$scope.no_more = true
				return
			$scope.comments = $scope.comments.concat(data.info)
			# $scope.show_result = true
	null
]);