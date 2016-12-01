@ng_app.controller("TeacCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', '$animate', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, $animate, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	$scope.schemes = [
		{
			name: "计算机科学与技术 博士",
			type: 0,
			id:  0
		},
		{
			name: "计算机科学与技术 学硕",
			type: 0,
			id:  1
		},
		{
			name: "计算机科学与技术 专硕",
			type: 0,
			id:  2
		},
		{
			name: "控制科学与工程 博士",
			type: 1,
			id:  3
		},
		{
			name: "控制科学与工程 学硕",
			type: 1,
			id:  4
		},
		{
			name: "控制工程与工程 专硕",
			type: 1,
			id:  5
		}
	]

	$scope.back_handler = ->
		if $scope.switching
			$('body').scrollTop(0)
			$scope.switching = false
		else
			if $window.history.length > 1
				$window.history.back()
			else
				$window.location = "./index.html"

	$scope.scheme_handler = (scheme_id) ->
		$window.location = "./scheme.html#?id=#{scheme_id}"

	null
]);









