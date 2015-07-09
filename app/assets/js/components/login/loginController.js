(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('loginController', ['middlewareService', 'authService', 'userService', 'alertService', '$scope', '$location', function(middlewareService, authService, userService, alertService, $scope, $location){
			
			middlewareService.unlogged();
			middlewareService.clearAlert();
						
			$scope.authService = authService;
			
			$scope.login = function(){
				
				authService.login($scope.username, $scope.password)
					.success(function(data){
						
						authService.setToken(data.token);
						userService.init();
						
						$location.path('/');
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
		}]);
		
})();