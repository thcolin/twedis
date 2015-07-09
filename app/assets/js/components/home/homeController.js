(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('homeController', ['middlewareService', 'tweetService', 'userService', 'alertService', '$scope', function(middlewareService, tweetService, userService, alertService, $scope){

			middlewareService.logged();
			middlewareService.clearAlert();
			middlewareService.initUser();
			
			$scope.tweets = [];
			$scope.page = 0;
			
			$scope.loadTweets = function(){
				
				tweetService.getTimeline($scope.page)
					.success(function(data){
						
						$scope.page++;
						
						angular.forEach(data.tweets, function(tweet, key){
							
							if($scope.tweets.indexOf(tweet) == -1)
							
								$scope.tweets.push(tweet);
							
						});
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
			$scope.tweet = function(){
				
				tweetService.tweet($scope.message)
					.success(function(data){
						
						console.log(data);
						
						alertService.setMessage(data.message);
						userService.currentUser.tweets++;
						$scope.tweets.unshift(data.tweet);
						$scope.message = null;
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
		}]);
		
})();