(function() {
  this.ng_app.directive('ncNav', function() {
    return {
      restrict: 'E',
      transclude: true,
      scope: {
        click_callback: '=backHandler'
      },
      link: function($scope, $element, $attrs, $ctrl, $transclude) {
        $scope.click_callback || ($scope.click_callback = function() {});
        return $transclude($scope, function(clone) {
          $scope.title = $compile(clone.html());
          return console.log($scope.title);
        });
      },
      template: "<div id=\"nc-nav\"> <div id=\"back-btn\" ng-click=\"click_callback()\"> <span> <ng-md-icon class=\"md-icon\" icon=\"navigate_before\" size=\"32\" id=\"group-add-icon\"></ng-md-icon> <span>返回</span> </span> </div> <div id=\"title\"> {{title}} </div> </div>"
    };
  });

}).call(this);
