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