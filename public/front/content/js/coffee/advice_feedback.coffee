
@ng_app.controller("AdviceFeedbackCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});

	$scope.go_main = ->
		$window.location = "./index.html"

	$scope.show_share_hint = ->
		$scope.show_hint = true

	$scope.cancel_share_hint = ->
		$scope.show_hint = false
		

])