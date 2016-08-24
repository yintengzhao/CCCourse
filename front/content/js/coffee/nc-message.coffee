@ng_app.directive('ncMessage', ()->
	return {
		restrict: 'E'
		transclude: true
		scope: {
			subject: '='
		}
		link: ($scope, $element, $attrs, $ctrl, $transclude)->
			$transclude($scope, (clone)->
				$scope.content = clone.html() || "该项为必填项"
			)
		template: 
			"<div ng-messages=\"subject.$error\" class=\"nc-message\">
              <div ng-message=\"required\">{{content}}</div>
            </div>"
	};
)