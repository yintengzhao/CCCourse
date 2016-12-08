@ng_app = angular.module(
		'CCCourse', 
		[
			'ngMaterial',
			'ngMdIcons',
			'ngResource',
			'internationalPhoneNumber',
			'ngMessages',
			'mainController',
			'courseController',
			'welcomeController',
			'commentResultController',
		]
	)
	.config ['$mdThemingProvider', 'ipnConfig', ($mdThemingProvider, ipnConfig)->
		$mdThemingProvider.theme('default')
		.primaryPalette('cyan')
		.accentPalette('red')

		ipnConfig.defaultCountry = 'cn'
		ipnConfig.preferredCountries = ['cn', 'us', 'uk', 'de', 'fr', 'ca']
		ipnConfig.skipUtilScriptDownload = true
	]
	.config ['$httpProvider', '$resourceProvider', (provider, resource)-> 
		login_state = localStorage.getItem("loginSession")
		provider.defaults.headers.common['Token'] = login_state
	]
	.run ['$rootScope', '$mdToast', ($rootScope, $mdToast)->
		$rootScope.range = (n)->
			[0...n]

		console.log 'module loading,,'

		$rootScope.popup_toast = (hint)->
			hint ||= "请正确填写表单"
			$mdToast.show($mdToast.simple().textContent(hint))
	]