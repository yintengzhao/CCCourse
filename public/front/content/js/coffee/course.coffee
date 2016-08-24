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

	
	NetManager.get("/Course/#{cid}/comments").then (data)->
		console.log data
		# $scope.comments = data.info
		for comment in data.info
			comment.score = comment.rank
			comment.content = comment.comment
		
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
			rank: c.score,
			comment: c.content
			why: []
		}
		ques = $scope.questions[0]
		for i in $scope.range(6)
			if ques.anwser[i]
				unless i==5
					params.why.push ques.options[i]
				else
					params.why.push ques.other

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