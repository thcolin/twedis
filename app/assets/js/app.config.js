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