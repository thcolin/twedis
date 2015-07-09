(function() {
    'use strict';
    
	angular
		.module('TwedisApp')
		.service('tweetService', ['$http', 'authService', function($http, authService){
			
			this.tweet = function(tweet){
			
				return $http({
					method  : 'POST',
					url     : 'api/tweet',
					data    : $.param({
						tweet: tweet
					}),
					headers : {'X-Token': authService.token}
				});
				
			};
			
			this.getTimeline = function(page){
				
				return $http({
					method  : 'GET',
					url     : 'api/tweets/timeline/' + page,
					headers : {'X-Token': authService.token}
				});
				
			};
			
			this.getByUsername = function(username, page){
				
				return $http({
					method  : 'GET',
					url     : 'api/tweets/user/' + username + '/' + page,
					headers : {'X-Token': authService.token}
				});
				
			};
			
			this.getByHashtag = function(hashtag, page){
				
				return $http({
					method  : 'GET',
					url     : 'api/tweets/hashtag/' + hashtag + '/' + page,
					headers : {'X-Token': authService.token}
				});
				
			};
			
		}]);
		
})();