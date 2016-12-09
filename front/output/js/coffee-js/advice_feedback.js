(function() {
  this.ng_app.controller("AdviceFeedbackCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager) {
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      $scope.go_main = function() {
        return $window.location = "./index.html";
      };
      $scope.show_share_hint = function() {
        return $scope.show_hint = true;
      };
      return $scope.cancel_share_hint = function() {
        return $scope.show_hint = false;
      };
    }
  ]);

}).call(this);
