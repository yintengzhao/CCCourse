
@ng_app.controller("HotCoursesCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	current_page = 1

	NetManager.get('/Course').then (data) ->
		return if data.status != 1
		$scope.courses = data.info
		if $scope.courses < 10
			$scope.no_more = true
			return

	$scope.course_handler = (cid)->
		$window.location = "./course.html#?cid=#{cid}" 


	$scope.back_handler = ->
		$window.location = "./index.html"

	$scope.load_more = ->
		current_page += 1
		request_parm = {key: "", page: current_page}
		NetManager.get("/Course/", request_parm).then (data)->
			# $scope.searching = false
			console.log data
			course_ar = data.info
			$scope.courses = $scope.courses.concat(course_ar)
			if course_ar.length < 10
				$scope.no_more = true
			# $scope.show_result = true
])