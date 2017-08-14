(function() {
    var app = angular.module('myApp.controllers', []);

    app.controller('CustomerCtrl', function($scope, $rootScope, $http) {
        
      	var load = function() {
    	        $http({
    	            url: $rootScope.appUrl + '/restaurant/customer/list',
    	            method: "POST",
    	            headers: {
    	            	'Content-Type': 'application/json'}
    	        })
    	        .success(function(data, status, headers, config) {
    		
    	        	$scope.customers = data.customers;
    	        });
      	}
      	
      	load();
      	
          $scope.clickToAdd = function () {
          };
          
    });
    
    app.controller('MonitorCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

      	var load = function() {
    	    	$http({
    	            url: $rootScope.appUrl + '/restaurant/order/monitor',
    	            method: "POST",
    	            headers: {
    	            	'Content-Type': 'application/json'}
    	        }) 
    	        .success(function(data, status, headers, config) {
    	
    	            $scope.orders = data.orders;
    	            $scope.orderitems = data.orderitems;
              });
      	}
      	
      	load();
      	
      });   
    
    app.controller('PinoutCtrl', function($scope, $rootScope, $http, $location) {

        $scope.submit = function() {
  	    	
        	$http({
  	            url: $rootScope.appUrl + '/restaurant/auth/pinoutdo',
  	            method: "POST",
  	            data: $scope.user,
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {
  	
  	        	$location.path('/ready');
            });       
        }
    });

    app.controller('PininCtrl', function($scope, $rootScope, $http, $location) {

        $scope.submit = function() {
  	    	
        	$http({
  	            url: $rootScope.appUrl + '/restaurant/auth/pinindo',
  	            method: "POST",
  	            data: $scope.user,
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {
  	
  	        	$location.path('/menu');
            });       
        }
    });
    
    app.controller('OrderCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	var load = function() {
  	    	$http({
  	            url: $rootScope.appUrl + '/restaurant/order/list',
  	            method: "POST",
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {
  	
  	            $scope.orders = data.orders;
  	            $scope.orderitems = data.orderitems;
            });
    	}
    	
    	load();

        $scope.receiptOrder = function(id) {
        	
        	$location.path('/receipt/' + id);
        }
        
        $scope.readyOrder = function(id) {

        	$http({
  	            url: $rootScope.appUrl + '/restaurant/order/incook',
  	            method: "POST",
  	            data: {id: id},
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {

            });           	
        }
        
        $scope.cancelOrder = function(id) {
        	
        	$http({
  	            url: $rootScope.appUrl + '/restaurant/order/cancel',
  	            method: "POST",
  	            data: {id: id},
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {

  	        	var order = getById($scope.orders, id)
  	        	
  	        	order.rstate = 'cancel';
            });        	
        }
        
    });

    app.controller('ReceiptCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	//alert(JSON.stringify($routeParams));
    	var load = function() {
  	    	$http({
  	            url: $rootScope.appUrl + '/restaurant/order/receipt',
  	            method: "POST",
  	            data: $routeParams,
  	            headers: {
  	            	'Content-Type': 'application/json'}
  	        }) 
  	        .success(function(data, status, headers, config) {
  	
  	        	$scope.restaurant = data.restaurant;
  	        	$scope.address = data.addresses[0];
  	        	$scope.order = data.order;
  	            $scope.orderitems = data.orderitems;

            });
    	}
    	
    	load();
    	
    	$scope.getSubTotal = function(){
    	    var total = 0;
    	    for(var i = 0; i < $scope.orderitems.length; i++){
    	        var item = $scope.orderitems[i];
    	        total += (item.price * item.qty);
    	    }
    	    return total;
    	}

    	$scope.getTax = function(){
    	    var total = 0;
    	    for(var i = 0; i < $scope.orderitems.length; i++){
    	        var item = $scope.orderitems[i];
    	        total += (item.price * item.qty);
    	    }
    	    return total * ($scope.restaurant.tax /100);
    	}
    	
    	$scope.getTotal = function(){
    	    var total = 0;
    	    for(var i = 0; i < $scope.orderitems.length; i++){
    	        var item = $scope.orderitems[i];
    	        total += (item.price * item.qty);
    	    }
    	    return total + total * ($scope.restaurant.tax /100);
    	}
    	
    });
    
}());