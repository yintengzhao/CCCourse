(function() {
  this.ng_app.directive('ncStar', function() {
    var calcu_star;
    calcu_star = function($scope, opt) {
      var min, score;
      score = Math.round(parseFloat($scope.score));
      score || (score = 0);
      if (score > 10) {
        score = 10;
      }
      min = (opt.min && +opt.min) || 0;
      if (score < min) {
        score = min;
      }
      $scope.fullstar = Math.floor(score / 2);
      if (opt.no_half) {
        $scope.halfstar = 0;
        $scope.fullstar += score % 2;
      } else {
        $scope.halfstar = score % 2;
      }
      return $scope.nullstar = 5 - $scope.fullstar - $scope.halfstar;
    };
    return {
      restrict: 'E',
      scope: {
        score: '=ngModel',
        size: '@',
        min: '@'
      },
      link: function($scope, $element, $attrs, $ctrl, $transclude) {
        $scope.range = function(n) {
          var i, results;
          return (function() {
            results = [];
            for (var i = 0; 0 <= n ? i < n : i > n; 0 <= n ? i++ : i--){ results.push(i); }
            return results;
          }).apply(this);
        };
        $scope.size || ($scope.size = 16);
        calcu_star($scope, $scope);
        $scope.no_half = $attrs.$attr.noHalf;
        return $scope.$watch('score', function(_, score) {
          return calcu_star($scope, $scope);
        });
      },
      template: '<span class="nc-star"> <span ng-repeat="i in range(fullstar)"> <ng-md-icon class="nc-star-icon md-icon" icon="star" size="{{size}}"></ng-md-icon> </span> <span ng-repeat="i in range(halfstar)"> <ng-md-icon class="nc-star-icon md-icon" icon="star_half" size="{{size}}"></ng-md-icon> </span> <span ng-repeat="i in range(nullstar)"> <ng-md-icon class="nc-star-icon md-icon" icon="star_border" size="{{size}}"></ng-md-icon> </span> </span>'
    };
  });

}).call(this);
