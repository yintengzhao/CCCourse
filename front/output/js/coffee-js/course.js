(function() {
  this.courseCtrl = angular.module('courseController', []);

  this.courseCtrl.controller("CourseCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager) {
      var cid, current_page;
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      cid = $location.search().cid;
      current_page = 1;
      NetManager.get("/Course/" + cid).then(function(data) {
        return $scope.course = data.info;
      });
      $scope.switching = false;
      $scope.comment = {};
      $scope.questions = [
        {
          options: ["感兴趣，有需要", "讲的好", "给分高", "容易过", "随便选的", "其他"],
          anwser: [],
          other: null
        }
      ];
      NetManager.get("/Course/" + cid + "/comments").then(function(data) {
        var comment, j, len, ref;
        console.log(data);
        ref = data.info;
        for (j = 0, len = ref.length; j < len; j++) {
          comment = ref[j];
          comment.score = comment.rank;
          comment.content = comment.comment;
        }
        $scope.comments = data.info;
        if ($scope.comments.length < 10) {
          return $scope.no_more = true;
        }
      });
      $scope.vote = function(c, action) {
        var net_action;
        if (action !== "up" && action !== "down") {
          console.log("wrong action!");
          return;
        }
        if (!c[action]) {
          net_action = NetManager.post;
        } else {
          net_action = NetManager["delete"];
        }
        return NetManager.post("/Evaluation/" + c.id + "/" + action).then(function(data) {
          console.log(data);
          if (+data.status === 1) {
            return NetManager.get("/Evaluation/" + c.id).then(function(data) {
              var info;
              if (+data.status !== 1) {
                return;
              }
              info = data.info;
              c.up = info.up;
              c.down = info.down;
              return c.vote = info.vote;
            });
          }
        });
      };
      $scope.comment_handler = function() {
        $('body').scrollTop(0);
        $scope.page_state = 2;
        return $scope.switching = true;
      };
      $scope.back_handler = function() {
        if ($scope.switching) {
          $('body').scrollTop(0);
          return $scope.switching = false;
        } else {
          if ($window.history.length > 1) {
            return $window.history.back();
          } else {
            return $window.location.href = "./index.html";
          }
        }
      };
      $scope.comment_submit = function() {
        var c, i, j, len, params, ques, ref;
        c = $scope.comment;
        c.score += c.score % 2;
        c.score = c.score > 1 ? c.score : 2;
        params = {
          cid: cid,
          rank: c.score,
          comment: c.content,
          why: []
        };
        ques = $scope.questions[0];
        ref = $scope.range(6);
        for (j = 0, len = ref.length; j < len; j++) {
          i = ref[j];
          if (ques.anwser[i]) {
            if (i !== 5) {
              params.why.push(ques.options[i]);
            } else {
              params.why.push(ques.other);
            }
          }
        }
        $scope.submiting = true;
        return NetManager.post("/Evaluation", params).then(function(data) {
          if (+data.status === 1) {
            return $window.location = "./comment_result.html";
          } else {
            console.log(data);
            $scope.submiting = false;
            return $scope.error_msg = data.info;
          }
        });
      };
      $scope.load_more = function() {
        var request_parm;
        current_page += 1;
        request_parm = {
          page: current_page
        };
        return NetManager.get("/Course/" + cid + "/comments", request_parm).then(function(data) {
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
      return null;
    }
  ]);

}).call(this);
