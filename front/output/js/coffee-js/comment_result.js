(function() {
  this.commentResultCtrl = angular.module('commentResultController', []);

  this.commentResultCtrl.controller("CommentResultCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager) {
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      NetManager.get("/Evaluation/count").then(function(data) {
        return $scope.count = +data.info;
      });
      $scope.go_advice = function() {
        return $window.location = "./advice.html";
      };
      $scope.back_search = function() {
        return $window.location = "./index.html";
      };
      return null;
    }
  ]);

}).call(this);
