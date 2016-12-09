(function() {
  this.ng_app.directive('ncMessage', function() {
    return {
      restrict: 'E',
      transclude: true,
      scope: {
        subject: '='
      },
      link: function($scope, $element, $attrs, $ctrl, $transclude) {
        return $transclude($scope, function(clone) {
          return $scope.content = clone.html() || "该项为必填项";
        });
      },
      template: "<div ng-messages=\"subject.$error\" class=\"nc-message\"> <div ng-message=\"required\">{{content}}</div> </div>"
    };
  });

}).call(this);
