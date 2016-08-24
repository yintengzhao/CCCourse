@welcomeCtrl = angular.module('welcomeController', []);

@welcomeCtrl.controller('WelcomeCtrl', 
	[
		'$controller', 
		'$scope',
		'$timeout',
		'$window',
		'$http',
		'$sce',
		'$animate',
		'NetManager', ($controller, $scope, $interval, $timeout, $window, $http, $sce, $animate, NetManager)->
			$scope.skip_login_check = true
			$controller('ApplicationCtrl', {$scope: $scope});

			null
	])