(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.config(['$routeProvider', function($routeProvider){
			
			// Routes
			
			$routeProvider
				.when('/login', {
					templateUrl: 'api/get/template/html/login.html',
					controller: 'loginController'
				})
				.when('/register', {
					templateUrl: 'api/get/template/html/register.html',
					controller: 'registerController'
				})
				.when('/', {
					templateUrl: 'api/get/template/html/home.html',
					controller: 'homeController'
				})
				.when('/user/:username/', {
					templateUrl: 'api/get/template/html/tweets.html',
					controller: 'tweetsController'
				})
				.when('/user/:username/followers', {
					templateUrl: 'api/get/template/html/followers.html',
					controller: 'followersController'
				})
				.when('/user/:username/following', {
					templateUrl: 'api/get/template/html/following.html',
					controller: 'followingController'
				})
				.when('/hashtag/:hashtag/', {
					templateUrl: 'api/get/template/html/hashtags.html',
					controller: 'hashtagsController'
				})
				.otherwise({
					redirectTo: '/'
				});
			
		}]);
		
})();