@welcomeCtrl = angular.module('welcomeController', []);

@welcomeCtrl.controller("WelcomeCtrl", [ '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', '$animate', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, $animate, NetManager)->	
	$scope.skip_login_check = true
	#$controller('ApplicationCtrl', {$scope: $scope});

	touch_trace = {start: 0, end: 0}

	$scope.hide_arrow = false
	$scope.swiping = false

	$scope.login = {}

	ele = $("#page-container")[0]
	slip = Slip(ele, 'y').webapp()

	slip.start (event)->
		# $scope.swiping = true
		$scope.$apply()

		if event.target.tagName == "svg"
			slip.jump(@page+1)

	
	slip.end ->
		$timeout(->
			$scope.swiping = false
		400)
		if @page==3
			$scope.hide_arrow = true
			# slip.slider().height(1200);
			slip.destroy()
			$timeout(->
				for i in [0..2]
					$("#welcome-#{i}").hide()
				$("#page-container").attr('style', '')
				$("#pre-loading").attr('style', '')
				$("#welcome").attr('style', '')
			400)
		$scope.$apply()

	#--------welcome-3---------

	NetManager.get('/Course/hot').then (data) ->
		return if(data.status != 1)
		console.log(data)
		$scope.hotCourses = data.info

	NetManager.get('/Advice/hot').then (data) ->
		return if(data.status != 1)
		console.log(data)
		$scope.hotAdvices = data.info

	$scope.course_handler = (type) ->
		$window.location = "./hot_courses.html?type=#{type}"


	null

])