(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('userController', ['userService', 'authService', 'alertService', '$scope', '$location', function(userService, authService, alertService, $scope, $location){
			
			$scope.authService = authService;
			$scope.userService = userService;
			
			$scope.logout = function(){
						
				authService.removeToken();
				$location.path('/login');
				
			};
			
		}]);
		
})();