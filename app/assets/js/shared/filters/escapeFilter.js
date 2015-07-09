(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.filter('escape', function(){
		
			return window.encodeURIComponent;
			
		});
		
})();