(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.service('middlewareService', ['authService', 'userService', 'alertService', '$location', function(authService, userService, alertService, $location){
			
			this.logged = function(){
				
				if(!authService.checkLogged())
				
					$location.path('/login');
				
			};
			
			this.unlogged = function(){
				
				if(authService.checkLogged())
				
					$location.path('/');
				
			};
			
			this.initUser = function(){
				
				userService.init();
				
			};
			
			this.clearAlert = function(){
				
				alertService.clearAlert();
				
			};
			
		}]);
		
})();