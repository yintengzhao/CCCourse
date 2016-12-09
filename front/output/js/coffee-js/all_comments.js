(function() {
  this.ng_app.controller("AllCommentsCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager) {
      var current_page;
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      current_page = 1;
      $scope.no_more = true;
      NetManager.get("/Advice").then(function(data) {
        console.log(data);
        $scope.comments = data.info;
        if ($scope.comments.length === 0) {
          return $scope.no_more = true;
        } else {
          return $scope.no_more = false;
        }
      });
      $scope.back_handler = function() {
        return $window.location = "./index.html";
      };
      $scope.go_advice = function() {
        return $window.location = "./advice.html";
      };
      $scope.vote = function(c, action) {
        if (action !== "up" && action !== "down") {
          console.log("wrong action!");
          return;
        }
        return NetManager.post("/Advice/" + c.id + "/" + action).then(function(data) {
          console.log(data);
          if (+data.status === 1) {
            return NetManager.get("/Advice/" + c.id).then(function(data) {
              var info;
              if (+data.status !== 1) {
                return;
              }
              info = data.info;
              c.vote = info.vote;
              c.up = info.up;
              return c.down = info.down;
            });
          }
        });
      };
      return $scope.load_more = function() {
        var request_parm;
        current_page += 1;
        request_parm = {
          page: current_page
        };
        return NetManager.get("/Advice", request_parm).then(function(data) {
          var course_ar;
          console.log(data);
          course_ar = data.info;
          if (course_ar.length === 0) {
            $scope.no_more = true;
            return;
          }
          return $scope.comments = $scope.comments.concat(data.info);
        });
      };
    }
  ]);

}).call(this);
