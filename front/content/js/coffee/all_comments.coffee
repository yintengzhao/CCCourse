
@ng_app.controller("AllCommentsCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});

	current_page = 1

	$scope.no_more = true

	NetManager.get("/Advice").then (data)->
		console.log data
		$scope.comments = data.info
		if $scope.comments.length == 0
			$scope.no_more = true
		else
			$scope.no_more = false


	$scope.back_handler = ->
		$window.location = "./index.html"

	$scope.go_advice = ->
		$window.location = "./advice.html"

	$scope.vote = (c, action)->
		if action!="up" && action!="down" then console.log("wrong action!"); return
		NetManager.post("/Advice/#{c.id}/#{action}").then (data)->
			console.log data
			if +data.status == 1
				NetManager.get("/Advice/#{c.id}").then (data)->
					return if +data.status!=1
					info = data.info
					c.vote = info.vote
					c.up = info.up
					c.down = info.down
	
	$scope.load_more = ->
		current_page += 1
		request_parm = {page: current_page}
		NetManager.get("/Advice", request_parm).then (data)->
			# $scope.searching = false
			console.log data
			course_ar = data.info
			if course_ar.length == 0
				$scope.no_more = true
				return
			$scope.comments = $scope.comments.concat(data.info)
			# $scope.show_result = true
])