(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('hashtagsController', ['middlewareService', 'tweetService', 'alertService', '$scope', '$routeParams', function(middlewareService, tweetService, alertService, $scope, $routeParams){

			middlewareService.logged();
			middlewareService.clearAlert();
			middlewareService.initUser();
			
			$scope.tweets = [];
			$scope.page = 0;
			
			$scope.loadTweets = function(){
				
				tweetService.getByHashtag($routeParams.hashtag, $scope.page)
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
			
		}]);
		
})();