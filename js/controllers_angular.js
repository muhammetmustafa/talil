var TestModulu = angular.module('Test', []);
		
TestModulu.controller('TestController', function($scope)
{
	$scope.deger = 0;
	
	$scope.hareket = function()
	{
		$scope.deger++;
		
		setTimeout($scope.hareket, 3000);
	};
});
