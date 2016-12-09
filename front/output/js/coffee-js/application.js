(function() {
  this.ng_app.controller('ApplicationCtrl', [
    "$scope", "$window", "NetManager", function($scope, $window, NetManager) {
      var check_state, wechat_init;
      check_state = function() {
        return NetManager.get('/User').then(function(data) {
          var clb;
          console.log(data);
          $scope.current_user = data.info;
          if (+data.status < 0) {
            $window.location = './welcome.html';
          }
          $scope.able_to_comment = $scope.current_user.type === "student";
          if ((clb = $scope.after_check_state)) {
            return clb();
          }
        });
      };
      wechat_init = function() {
        var wechat;
        if (!wx) {
          return;
        }
        NetManager.get("/Wechat/token").then(function(data) {
          console.log(data);
          if (+data.status === 1) {
            return wechat(data.info);
          }
        });
        return wechat = function(sign_info) {
          var config, host;
          wx.config({
            debug: false,
            appId: sign_info.appId,
            timestamp: sign_info.timestamp,
            nonceStr: sign_info.nonceStr,
            signature: sign_info.signature,
            jsApiList: ['checkJsApi', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone']
          });
          host = $window.location.host;
          config = {
            title: '南开时“课”（公选课版）',
            link: "http://" + host + "/welcome.html",
            imgUrl: "http://" + host + "/assets/wechat.jpg",
            trigger: function(res) {},
            success: function(res) {},
            cancel: function(res) {},
            fail: function(res) {}
          };
          if ($scope.current_user) {
            config.desc = "我是第" + $scope.current_user.id + "个参与南开公选课评价和建议的校友，你也快来参加吧";
          } else {
            config.desc = '那些年，那些一起上过的公选课，倒影在你我的青春里';
          }
          return wx.ready(function() {
            wx.onMenuShareAppMessage(config);
            wx.onMenuShareTimeline(config);
            wx.onMenuShareQQ(config);
            wx.onMenuShareWeibo(config);
            return wx.onMenuShareQZone(config);
          });
        };
      };
      if (!$scope.skip_login_check) {
        $scope.after_check_state = wechat_init;
        return check_state();
      } else {
        return wechat_init();
      }
    }
  ]);

}).call(this);
