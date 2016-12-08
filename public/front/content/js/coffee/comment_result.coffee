@commentResultCtrl = angular.module('commentResultController', []);



@commentResultCtrl.controller("CommentResultCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	NetManager.get("/Evaluation/count").then (data)->
		$scope.count = +data.info

	$scope.back_search = ->
		$window.location = "./index.html"

	$scope.show_share_hint = ->
		$scope.show_hint = true

	$scope.cancel_share_hint = ->
		$scope.show_hint = false
	null
])