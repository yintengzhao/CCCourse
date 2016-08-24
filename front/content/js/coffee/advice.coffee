
@ng_app.controller("AdviceCtrl", ['$controller',  '$scope', '$interval', '$timeout', '$window', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	
	$scope.page_state = 1
	$scope.advice = {}
	$scope.contact = {}

	$scope.questions = [
		{
			options: [ "有", "没有" ,"不好说"]
			value: [ 1, -1 , 0]
			anwser: null
		}
		{
			options: [
				"理工类", "人文类", "社科类"
				"艺术类", "实践类", "其他"
			]
			keys: [
				"technology",	"humanity",	"social"
				"art", 			"practice",	"other"
			]
			anwser: {}
		}
	]
	
	$scope.submit_handler = ->
		form =  $scope.advice_form
		form.$setSubmitted()
		
		if form.$invalid
			$scope.popup_toast()
			return
		
		$scope.submiting = true
		NetManager.post("/Advice", $scope.advice).then (data)->
			NetManager.get("/Contact").then (data)->
				$scope.submiting = false
				if +data.status != 1
					$scope.page_state = 2
				else
					$window.location = "./advice_feedback.html"

	$scope.on_advice_page = ->
		$scope.page_state == 1

	$scope.user_info_skip = ->
		$window.location = "./advice_feedback.html"


	$scope.user_info_submit = ->

		params = {}
		angular.copy($scope.contact, params)

		form = $scope.contact_form
		form.$setSubmitted()
		phone_error =  form.$error.internationalPhoneNumber
		delete params.phone
		if !phone_error
			params.phone =  $('#form-phone-number input').intlTelInput('getNumber')

		flag = false
		for k,v of $scope.contact
			console.log [k,v]
			if v && v != "" then flag = true else delete params[k]

		flag &&= (!form.$invalid)

		console.log params
		console.log flag

		unless flag
			console.log $scope.contact_form.mail.$error

			$scope.popup_toast()
			return

		$scope.submiting = true

		NetManager.post("/Contact/", params).then (data)->
			$scope.submiting = false
			if +data.status ==1
				$window.location = "./advice_feedback.html"
			else
				console.log data
				# $scope.error_msg = data.info
				$scope.popup_toast(data.info)

])