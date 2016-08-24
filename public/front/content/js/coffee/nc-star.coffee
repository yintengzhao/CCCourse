@ng_app.directive('ncStar', ()->
	calcu_star = ($scope, opt)->
		score = Math.round(parseFloat($scope.score))
		score ||= 0
		if score > 10 then score = 10
		min = (opt.min && +opt.min) || 0
		if score < min then score = min
		$scope.fullstar = Math.floor(score/2)
		if opt.no_half
			$scope.halfstar = 0
			$scope.fullstar += score%2
		else
			$scope.halfstar = score%2
		$scope.nullstar = 5-$scope.fullstar-$scope.halfstar

	return {
		restrict: 'E'
		scope: {
			score: '=ngModel'
			size: '@'
			min: '@'
		}
		link: ($scope, $element, $attrs, $ctrl, $transclude)->
			$scope.range = (n)-> [0...n]
			$scope.size ||= 16

			calcu_star($scope, $scope)

			$scope.no_half = $attrs.$attr.noHalf

			$scope.$watch('score', (_, score)->
				calcu_star($scope, $scope)
			)
		template: 
			'
			<span class="nc-star">
				<span ng-repeat="i in range(fullstar)">
				<ng-md-icon class="nc-star-icon md-icon" icon="star" size="{{size}}"></ng-md-icon>
				</span>
				<span ng-repeat="i in range(halfstar)">
				<ng-md-icon class="nc-star-icon md-icon" icon="star_half" size="{{size}}"></ng-md-icon>
				</span>
				<span ng-repeat="i in range(nullstar)">
				<ng-md-icon class="nc-star-icon md-icon" icon="star_border" size="{{size}}"></ng-md-icon>
				</span>
			</span>
			'
	};
)