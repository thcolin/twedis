(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.factory('userFactory', ['$http', 'authService', 'alertService', function($http, authService, alertService){
			
			var userFactory = function(values){
				
				// Construct
				
				angular.forEach(values, function(value, key){
					
					this[key] = value;
					
				}, this);
				
				// Follow
				
				this.isFollowing = function(username){
					
					return (this.following.indexOf(username) != -1);
					
				};
				
				this.gotFollower = function(username){
					
					return (this.followers.indexOf(username) != -1);
					
				};
				
				this.toggleFollow = function(user){
					
					var that = this;
					var follow = (this.isFollowing(user.username) ? 0:1);
					
					$http({
						method  : 'POST',
						url     : 'api/user/' + user.username + '/toggleFollow/' + follow,
						headers : {'X-Token': authService.token}
					})
					.success(function(data){
						
						if(follow){
						
							that.following.push(user.username);
							
							if(user.followers)
							
								user.followers.push(that.username);
							
						}
							
						else{
						
							that.following.splice(that.following.indexOf(user.username), 1);
							
							if(user.followers)
							
								user.followers.splice(user.following.indexOf(that.username), 1);
							
						}
						
					})
					.error(function(data){
						
						
						alertService.setError(data.message);
						
					});
					
				};
				
			};
			
			return userFactory;
			
		}]);
		
})();