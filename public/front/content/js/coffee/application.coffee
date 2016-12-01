@ng_app.controller('ApplicationCtrl', 
	["$scope", "$window", "NetManager", ($scope, $window, NetManager)->
		check_state = ->
			NetManager.get('/User').then (data)->
				console.log(data)
				$scope.current_user = data.info
				if +data.status < 0
					$window.location = './welcome.html'

				$scope.able_to_comment = $scope.current_user.type == "student"
				if($scope.comment_cbk)
					$scope.comment_cbk()
				if(clb = $scope.after_check_state)
					clb()

		wechat_init = ->
			unless wx then return
			NetManager.get("/Wechat/token").then (data)->
				console.log data
				if +data.status == 1
					wechat(data.info)

			wechat = (sign_info)->
				wx.config({
					debug: false,
					appId: sign_info.appId,
					timestamp: sign_info.timestamp,
					nonceStr: sign_info.nonceStr,
					signature: sign_info.signature,
					jsApiList: [
						'checkJsApi',
						'onMenuShareTimeline',
						'onMenuShareAppMessage',
						'onMenuShareQQ',
						'onMenuShareWeibo',
						'onMenuShareQZone',
					]
				});

				host = $window.location.host
				config = {
					title: '计控评课',
					link: "http://#{host}/welcome.html",
					imgUrl: "http://#{host}/assets/wechat.jpg",
					trigger: (res)->
						# 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
						# alert('用户点击分享');
					success: (res)->
						#alert('已分享')
					cancel: (res)->
						#alert('已取消')
					fail: (res)->
						#alert(JSON.stringify(res));
				}

				if $scope.current_user
					config.desc = "我是第#{$scope.current_user.id}个参与南开公选课评价和建议的校友，你也快来参加吧"
				else 
					config.desc = '那些年，那些一起上过的公选课，倒影在你我的青春里'

				wx.ready(()->
					wx.onMenuShareAppMessage(config);
					wx.onMenuShareTimeline(config);
					wx.onMenuShareQQ(config);
					wx.onMenuShareWeibo(config);
					wx.onMenuShareQZone(config);
				)

		unless $scope.skip_login_check
			$scope.after_check_state = wechat_init
			check_state()
		else
			wechat_init()

]);