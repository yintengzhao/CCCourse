@courseCtrl = angular.module('courseController', []);


@courseCtrl.controller("CourseCtrl", ['$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager)->
	#$controller('ApplicationCtrl', {$scope: $scope});
	#------write by foreve_---------
	$scope.able_to_comment = true

	#---------

	cid = $location.search().cid
	current_page = 1

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
			answer: []
			other: null
		}
	]

	NetManager.get("/Course/#{cid}/info").then (data)->
		console.log data
		if data.status
			$scope.course = data.info

	NetManager.get("/Course/#{cid}/comments").then (data)->
		console.log data
		if data.status
			$scope.comments = data.info
			if $scope.comments.length < 10
				$scope.no_more = true

	$scope.comment_handler = ->
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
				$window.location.href = "./welcome.html"
	
	null
]);