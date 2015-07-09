(function() {
    'use strict';
    
	angular
		.module('TwedisApp')
		.service('userService', ['authService', 'alertService', 'userFactory', '$http', function(authService, alertService, userFactory, $http){
			
			this.currentUser = null;
			
			this.getByUsername = function(username){
				
				return $http({
					method  : 'GET',
					url     : 'api/user/' + username,
					headers : {'X-Token': authService.token}
				});
				
			};
			
			this.init = function(){
				
				var that = this;
				
				$http({
					method  : 'GET',
					url     : 'api/user',
					headers : {'X-Token': authService.token}
				})
				.success(function(data){
					
					that.currentUser = new userFactory(data.user);
					
				})
				.error(function(data){
					
					alertService.setError(data.message);
					
				});
				
			};
			
		}]);
		
})();