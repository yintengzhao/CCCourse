(function() {
  this.ng_app.controller("HotCoursesCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager) {
      var current_page;
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      current_page = 1;
      NetManager.get('/Course').then(function(data) {
        if (data.status !== 1) {
          return;
        }
        $scope.courses = data.info;
        if ($scope.courses < 10) {
          $scope.no_more = true;
        }
      });
      $scope.course_handler = function(cid) {
        return $window.location = "./course.html#?cid=" + cid;
      };
      $scope.back_handler = function() {
        return $window.location = "./index.html";
      };
      return $scope.load_more = function() {
        var request_parm;
        current_page += 1;
        request_parm = {
          key: "",
          page: current_page
        };
        return NetManager.get("/Course/", request_parm).then(function(data) {
          var course_ar;
          console.log(data);
          course_ar = data.info;
          $scope.courses = $scope.courses.concat(course_ar);
          if (course_ar.length < 10) {
            return $scope.no_more = true;
          }
        });
      };
    }
  ]);

}).call(this);
