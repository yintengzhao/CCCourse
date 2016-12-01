@ng_app.directive('ncNav', ()->
	return {
		restrict: 'E'
		transclude: true
		scope: {
			click_callback: '=backHandler'
		}
		link: ($scope, $element, $attrs, $ctrl, $transclude)->
			$scope.click_callback ||= ->
			$transclude($scope, (clone)->
				$scope.title = clone.html()
			)
		template: 
			"<div id=\"nc-nav\">
				<div id=\"back-btn\" ng-click=\"click_callback()\">
					<span>	
						<ng-md-icon class=\"md-icon\" icon=\"navigate_before\" size=\"32\" id=\"group-add-icon\"></ng-md-icon>
						<span>返回</span>
					</span>
				</div>
				<div id=\"title\" ng-transclude>
					{{title}}
				</div>
			</div>"
	};
)