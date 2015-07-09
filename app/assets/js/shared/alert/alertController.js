(function() {
    'use strict';

	angular
		.module('TwedisApp')
		.controller('alertController', ['alertService', '$scope', function(alertService, $scope){

			$scope.alertService = alertService;
			
		}]);
		
})();