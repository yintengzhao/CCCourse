(function() {
  this.welcomeCtrl = angular.module('welcomeController', []);

  this.welcomeCtrl.controller("WelcomeCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', '$animate', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, $animate, NetManager) {
      var ele, slip, touch_trace;
      $scope.skip_login_check = true;
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      touch_trace = {
        start: 0,
        end: 0
      };
      $scope.hide_arrow = false;
      $scope.swiping = false;
      $scope.login = {};
      ele = $("#page-container")[0];
      slip = Slip(ele, "y").webapp();
      slip.start(function(event) {
        $scope.swiping = true;
        $scope.$apply();
        if (event.target.tagName === "svg") {
          return slip.jump(this.page + 1);
        }
      });
      slip.end(function() {
        $timeout(function() {
          return $scope.swiping = false;
        }, 400);
        if (this.page === 3) {
          $scope.hide_arrow = true;
          slip.destroy();
          $timeout(function() {
            var i, j;
            for (i = j = 0; j <= 2; i = ++j) {
              $("#welcome-" + i).hide();
            }
            $("#page-container").attr('style', '');
            $("#pre-loading").attr('style', '');
            return $("#welcome").attr('style', '');
          }, 400);
        }
        return $scope.$apply();
      });
      $scope.submit = function() {
        var form;
        form = $scope.login_form;
        form.$setSubmitted();
        if (form.$invalid) {
          $scope.popup_toast();
          return;
        }
        $scope.submiting = true;
        return NetManager.post('/User', $scope.login).then(function(data) {
          $scope.submiting = false;
          if (+data.status > 0) {
            localStorage.setItem("loginSession", data.info.token);
            return $window.location = "./index.html";
          } else {
            return $scope.popup_toast(data.info);
          }
        });
      };
      $scope.toggle_forget = function() {
        return $scope.in_forget = !$scope.in_forget;
      };
      $animate.enabled(false, $('#jwc-qr'));
      return null;
    }
  ]);

}).call(this);
