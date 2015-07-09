(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('tweetsController', ['middlewareService', 'tweetService', 'userService', 'alertService', '$scope', '$routeParams', function(middlewareService, tweetService, userService, alertService, $scope, $routeParams){

			middlewareService.logged();
			middlewareService.clearAlert();
			middlewareService.initUser();
			
			$scope.tweets = [];
			$scope.page = 0;
			
			$scope.user = null;
			$scope.userService = userService;
			
			$scope.loadTweets = function(){
				
				tweetService.getByUsername($routeParams.username, $scope.page)
					.success(function(data){
						
						angular.forEach(data.tweets, function(tweet, key){
							
							if($scope.tweets.indexOf(tweet) == -1)
							
								$scope.tweets.push(tweet);
							
						});
						
						$scope.page++;
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
					
			};
			
			$scope.init = function(){
					
				userService.getByUsername($routeParams.username)
					.success(function(data){
						
						$scope.user = data.user;
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
			$scope.init();
			
		}]);
		
})();