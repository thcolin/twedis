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