@welcomeCtrl = angular.module('welcomeController', []);



@welcomeCtrl.controller("WelcomeCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', '$animate', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, $animate, NetManager)->
	$scope.skip_login_check = true
	$controller('ApplicationCtrl', {$scope: $scope});

	touch_trace  = {start:0, end:0}

	$scope.hide_arrow = false
	$scope.swiping = false

	$scope.login = {}
	


	ele = $("#page-container")[0];

	slip = Slip(ele, "y").webapp();

	# slip.jump(3)
	# $scope.in_forget = true

	slip.start (event)->
		$scope.swiping = true
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

	$scope.submit = ->
		form = $scope.login_form
		form.$setSubmitted()
		
		if form.$invalid
			$scope.popup_toast()
			return


		$scope.submiting = true
		NetManager.post('/User', $scope.login).then (data)->
			$scope.submiting = false
			if +data.status > 0
				localStorage.setItem("loginSession", data.info.token)
				$window.location = "./index.html"
			else
				$scope.popup_toast(data.info)

	$scope.toggle_forget = ->
		$scope.in_forget = !$scope.in_forget

	# http://davidchin.me/blog/disable-nganimate-for-selected-elements/
	$animate.enabled(false, $('#jwc-qr'));
	# trigger_swiping()
	null
]);









