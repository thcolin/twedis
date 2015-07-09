(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('followingController', ['middlewareService', 'userService', 'userFactory', 'alertService', '$scope', '$routeParams', function(middlewareService, userService, userFactory, alertService, $scope, $routeParams){

			middlewareService.logged();
			middlewareService.clearAlert();
			middlewareService.initUser();
			
			$scope.user = null;
			$scope.userService = userService;
			
			$scope.init = function(){
				
				userService.getByUsername($routeParams.username)
					.success(function(data){
						
						$scope.user = new userFactory(data.user);
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
			$scope.init();
			
		}]);
		
})();