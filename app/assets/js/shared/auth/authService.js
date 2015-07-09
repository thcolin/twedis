(function() {
    'use strict';
    
	angular
		.module('TwedisApp')
		.service('authService', ['$http', '$rootScope', 'localStorageService', function($http, $rootScope, localStorageService){
			
			// Global
			
			var checkLogged = function(){
				
				return (localStorageService.get('TwedisToken') ? true:false);
				
			};
			
			// Vars
			
			this.token = localStorageService.get('TwedisToken');
			this.isLogged = checkLogged();
			
			// Functions
			
			this.checkLogged = function(){
				
				return checkLogged();
				
			}
			
			this.setToken = function(token){
				
				localStorageService.set('TwedisToken', token);
				this.isLogged = checkLogged();
				this.token = token;
				
			};
			
			this.removeToken = function(){
				
				localStorageService.remove('TwedisToken');
				this.isLogged = checkLogged();
				this.token = null;
				
			};
			
			this.login = function(username, password){
			
				return $http({
					method : 'POST',
					url    : 'api/login',
					data   : $.param({
						username: username,
						password: password
					})
				});
				
			};
			
			this.register = function(username, password, confirm){
			
				return $http({
					method : 'POST',
					url    : 'api/register',
					data   : $.param({
						username: username,
						password: password,
						confirm : confirm
					})
				});
				
			};
			
		}]);
		
})();