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