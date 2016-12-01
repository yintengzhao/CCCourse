@mainCtrl = angular.module('mainController', []);



# The main contoller logic
@mainCtrl.controller("MainCtrl", ['$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager)->
	$scope.comment_cbk = ->
		if $scope.current_user.type == "teacher"
			$window.location = './teac.html'

	appCtrl = $controller('ApplicationCtrl', {$scope: $scope});

	$scope.show_result = false
	$scope.courses = []

	current_page = 1
	current_key = null
	
	$scope.course_handler = (cid)->
		$window.location = "./course.html#?cid=#{cid}" 

	$scope.go_search = (key, page)->
		$scope.no_more = false
		current_key = key ||= ""
		current_page = page ||= 1
		$scope.searching = true
		request_parm = {key: key, page: page}
		NetManager.get("/Course/", request_parm).then (data)->
			$location.search(request_parm)
			$scope.searching = false
			console.log data
			$scope.courses = data.info
			$scope.show_result = true
			$('body').scrollTop(0)
			if data.info.length < 10
				$scope.no_more = true


		# $window.location = "./result.html"

	$scope.back_search = ->
		$location.search("")
		$scope.courses = null
		$scope.show_result = false

	$scope.go_advice = ->
		$window.location = "./advice.html"

	$scope.load_more = ->
		current_page += 1
		console.log current_key
		request_parm = {key: current_key, page: current_page}
		NetManager.get("/Course/", request_parm).then (data)->
			# $scope.searching = false
			console.log data
			course_ar = data.info
			if course_ar.length < 10
				$scope.no_more = true
				return
			$scope.courses = $scope.courses.concat(data.info)
			# $scope.show_result = true

	if (search = $location.search()).key != undefined
		$scope.go_search(search.key, +search.page)
		current_key = search.key

	null
]);









