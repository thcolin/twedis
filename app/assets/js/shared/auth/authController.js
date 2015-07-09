(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('authController', ['authService', '$scope', function(authService, $scope){
						
			$scope.authService = authService;
			
		}]);
		
})();