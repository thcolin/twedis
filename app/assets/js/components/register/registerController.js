(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('registerController', ['middlewareService', 'authService', 'alertService', '$scope', '$location', function(middlewareService, authService, alertService, $scope, $location){
			
			middlewareService.unlogged();
			middlewareService.clearAlert();
			
			$scope.register = function(){
				
				authService.register($scope.username, $scope.password, $scope.confirm)
					.success(function(data){
						
						$location.path('/login');
						alertService.setMessage(data.message);
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
		}]);
		
})();