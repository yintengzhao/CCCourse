(function() {
  this.ng_app = angular.module('NKUCourse', ['ngMaterial', 'ngMdIcons', 'ngResource', 'internationalPhoneNumber', 'ngMessages', 'mainController', 'welcomeController', 'courseController', 'commentResultController']).config([
    '$mdThemingProvider', 'ipnConfig', function($mdThemingProvider, ipnConfig) {
      $mdThemingProvider.theme('default').primaryPalette('cyan').accentPalette('blue');
      ipnConfig.defaultCountry = 'cn';
      ipnConfig.preferredCountries = ['cn', 'us', 'uk', 'de', 'fr', 'ca'];
      return ipnConfig.skipUtilScriptDownload = true;
    }
  ]).config([
    "$httpProvider", '$resourceProvider', function(provider, resource) {
      var login_state;
      login_state = localStorage.getItem("loginSession");
      return provider.defaults.headers.common['Token'] = login_state;
    }
  ]).run([
    '$rootScope', '$mdToast', function($rootScope, $mdToast) {
      $rootScope.range = function(n) {
        var i, results;
        return (function() {
          results = [];
          for (var i = 0; 0 <= n ? i < n : i > n; 0 <= n ? i++ : i--){ results.push(i); }
          return results;
        }).apply(this);
      };
      return $rootScope.popup_toast = function(hint) {
        hint || (hint = "请正确填写表单");
        return $mdToast.show($mdToast.simple().textContent(hint));
      };
    }
  ]);

}).call(this);
