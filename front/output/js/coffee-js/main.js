(function() {
  this.mainCtrl = angular.module('mainController', []);

  this.mainCtrl.controller("MainCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager) {
      var current_key, current_page, search;
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      $scope.show_result = false;
      $scope.courses = [];
      current_page = 1;
      current_key = null;
      $scope.course_handler = function(cid) {
        return $window.location = "./course.html#?cid=" + cid;
      };
      $scope.go_search = function(key, page) {
        var request_parm;
        $scope.no_more = false;
        current_key = key || (key = "");
        current_page = page || (page = 1);
        $scope.searching = true;
        request_parm = {
          key: key,
          page: page
        };
        return NetManager.get("/Course/", request_parm).then(function(data) {
          $location.search(request_parm);
          $scope.searching = false;
          console.log(data);
          $scope.courses = data.info;
          $scope.show_result = true;
          $('body').scrollTop(0);
          if (data.info.length < 10) {
            return $scope.no_more = true;
          }
        });
      };
      $scope.back_search = function() {
        $location.search("");
        $scope.courses = null;
        return $scope.show_result = false;
      };
      $scope.go_advice = function() {
        return $window.location = "./advice.html";
      };
      $scope.load_more = function() {
        var request_parm;
        current_page += 1;
        console.log(current_key);
        request_parm = {
          key: current_key,
          page: current_page
        };
        return NetManager.get("/Course/", request_parm).then(function(data) {
          var course_ar;
          console.log(data);
          course_ar = data.info;
          if (course_ar.length < 10) {
            $scope.no_more = true;
            return;
          }
          return $scope.courses = $scope.courses.concat(data.info);
        });
      };
      if ((search = $location.search()).key !== void 0) {
        $scope.go_search(search.key, +search.page);
        current_key = search.key;
      }
      return null;
    }
  ]);

}).call(this);
