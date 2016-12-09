(function() {
  this.ng_app.controller("AdviceCtrl", [
    '$controller', '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager', function($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager) {
      $controller('ApplicationCtrl', {
        $scope: $scope
      });
      $scope.page_state = 1;
      $scope.advice = {};
      $scope.contact = {};
      $scope.questions = [
        {
          options: ["有", "没有", "不好说"],
          value: [1, -1, 0],
          anwser: null
        }, {
          options: ["理工类", "人文类", "社科类", "艺术类", "实践类", "其他"],
          keys: ["technology", "humanity", "social", "art", "practice", "other"],
          anwser: {}
        }
      ];
      $scope.submit_handler = function() {
        var form;
        form = $scope.advice_form;
        form.$setSubmitted();
        if (form.$invalid) {
          $scope.popup_toast();
          return;
        }
        $scope.submiting = true;
        return NetManager.post("/Advice", $scope.advice).then(function(data) {
          return NetManager.get("/Contact").then(function(data) {
            $scope.submiting = false;
            if (+data.status !== 1) {
              return $scope.page_state = 2;
            } else {
              return $window.location = "./advice_feedback.html";
            }
          });
        });
      };
      $scope.on_advice_page = function() {
        return $scope.page_state === 1;
      };
      $scope.user_info_skip = function() {
        return $window.location = "./advice_feedback.html";
      };
      return $scope.user_info_submit = function() {
        var flag, form, k, params, phone_error, ref, v;
        params = {};
        angular.copy($scope.contact, params);
        form = $scope.contact_form;
        form.$setSubmitted();
        phone_error = form.$error.internationalPhoneNumber;
        delete params.phone;
        if (!phone_error) {
          params.phone = $('#form-phone-number input').intlTelInput('getNumber');
        }
        flag = false;
        ref = $scope.contact;
        for (k in ref) {
          v = ref[k];
          console.log([k, v]);
          if (v && v !== "") {
            flag = true;
          } else {
            delete params[k];
          }
        }
        flag && (flag = !form.$invalid);
        console.log(params);
        console.log(flag);
        if (!flag) {
          console.log($scope.contact_form.mail.$error);
          $scope.popup_toast();
          return;
        }
        $scope.submiting = true;
        return NetManager.post("/Contact/", params).then(function(data) {
          $scope.submiting = false;
          if (+data.status === 1) {
            return $window.location = "./advice_feedback.html";
          } else {
            console.log(data);
            return $scope.popup_toast(data.info);
          }
        });
      };
    }
  ]);

}).call(this);
