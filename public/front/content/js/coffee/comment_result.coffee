@commentResultCtrl = angular.module('commentResultController', []);



@commentResultCtrl.controller("CommentResultCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	NetManager.get("/Evaluation/count").then (data)->
		$scope.count = +data.info

	$scope.go_advice = ->
		$window.location = "./advice.html"

	$scope.back_search = ->
		$window.location = "./index.html"
	null
])