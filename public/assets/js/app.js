(function() {
    'use strict';
    
    angular
		.module('TwedisApp', ['ngRoute', 'LocalStorageModule', 'angularMoment', 'infinite-scroll']);
		
})();
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.config(['$locationProvider', function($locationProvider){
			
			// HTML5 Mode (no #)
			//$locationProvider.html5Mode(true);
			
		}])
		.run(['$http', 'amMoment', function($http, amMoment){
			
			// HTTP headers
			
			$http.defaults.headers.post = {
				'Content-Type': 'application/x-www-form-urlencoded'
			};
			
			// Moment
			
			amMoment.changeLocale('fr');
			
		}])
		.constant('angularMomentConfig', {
			
			preprocess: 'unix', // optional
			timezone: 'Europe/Paris' // optional
		
		});
		
})();
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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('followersController', ['middlewareService', 'userService', 'userFactory', 'alertService', '$scope', '$routeParams', function(middlewareService, userService, userFactory, alertService, $scope, $routeParams){

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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('loginController', ['middlewareService', 'authService', 'userService', 'alertService', '$scope', '$location', function(middlewareService, authService, userService, alertService, $scope, $location){
			
			middlewareService.unlogged();
			middlewareService.clearAlert();
						
			$scope.authService = authService;
			
			$scope.login = function(){
				
				authService.login($scope.username, $scope.password)
					.success(function(data){
						
						authService.setToken(data.token);
						userService.init();
						
						$location.path('/');
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
		}]);
		
})();
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('registerController', ['middlewareService', 'authService', 'alertService', '$scope', '$location', function(middlewareService, authService, alertService, $scope, $location){
			
			middlewareService.unlogged();
			middlewareService.clearAlert();
			
			$scope.register = function(){
				
				authService.register($scope.username, $scope.password, $scope.confirm)
					.success(function(data){
						
						$location.path('/login');
						alertService.setMessage(data.message);
						
					})
					.error(function(data){
						
						alertService.setError(data.message);
						
					});
				
			};
			
		}]);
		
})();
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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('alertController', ['alertService', '$scope', function(alertService, $scope){

			$scope.alertService = alertService;
			
		}]);
		
})();
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.service('alertService', [function(){
			
			this.alert = null;
			
			this.setAlert = function(style, icon, message){
				
				this.alert = {
					style   : style,
					icon    : icon,
					message : message
				};
				
			};
			
			this.setMessage = function(message){
				
				if(message)
			
					this.alert = {
						style   : 'alert-success',
						icon    : 'fa-thumbs-o-up',
						message : message	
					};
				
			};
			
			this.setError = function(message){
				
				if(message)
			
					this.alert = {
						style   : 'alert-danger',
						icon    : 'fa-exclamation-triangle',
						message : message
					};
				
			};
			
			this.clearAlert = function(){
				
				this.alert = null;
				
			}
			
		}]);
		
})();
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('authController', ['authService', '$scope', function(authService, $scope){
						
			$scope.authService = authService;
			
		}]);
		
})();
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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.filter('escape', function(){
		
			return window.encodeURIComponent;
			
		});
		
})();
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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.directive('message', function(){
			
			return {
				scope:{
					message: '=message'
				},
				link: function(scope, element, attrs){
					
					var message = scope.message.replace(/#([a-zA-Z0-9]+)/gi, '<a href="#/hashtag/$1">$&</a>');
					element.replaceWith('<p>' + message + '</p>');
					
				}
			};
			
		});
		
})();
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.directive('tweet', function(){
			
			return {
				scope:{
					tweet: '=tweet'
				},
				restrict: 'EA',
				templateUrl: 'api/get/template/html/tweet.html'
			};
			
		});
		
})();

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
(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('userController', ['userService', 'authService', 'alertService', '$scope', '$location', function(userService, authService, alertService, $scope, $location){
			
			$scope.authService = authService;
			$scope.userService = userService;
			
			$scope.logout = function(){
						
				authService.removeToken();
				$location.path('/login');
				
			};
			
		}]);
		
})();
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