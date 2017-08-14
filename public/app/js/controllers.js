(function() {
    var as = angular.module('myApp.controllers', []);
    
    as.controller('AppCtrl', function($scope, $rootScope, $http, i18n, $location) {
/*        $scope.language = function() {
            return i18n.language;
        };
        $scope.setLanguage = function(lang) {
            i18n.setLanguage(lang);
        };
        $scope.activeWhen = function(value) {
            return value ? 'active' : '';
        };

        $scope.path = function() {
            return $location.url();
        };*/

        $rootScope.appUrl = "http://baapchef.localhost";
    });

    as.controller('LoginCtrl', function($scope, $rootScope, $http, $location) {

    	$scope.login = function() {

		}
    });

    as.controller('SignupCtrl', function($scope, $rootScope, $http, $location) {

    	$scope.signup = function() {

		}
    });
    
    as.controller('TakeoutListCtrl', function($scope, $rootScope, $http, $location) {
        
    	var load = function() {
            console.log('call load()...');
            $http({
                url: $rootScope.appUrl + '/customer/takeoutdish',
                method: "POST",
                headers: {
                	'Content-Type': 'application/json'}
            })
            .success(function(data, status, headers, config) {
    	
    		    $scope.takeoutdishes = data.dishes;
                angular.copy($scope.takeoutdishes, $scope.copy);
            });
        }

        load();

        $scope.makeOrder = function() {
        	
        	$http({
                url: $rootScope.appUrl + '/customer/order',
                method: "POST",
                headers: {
                	'Content-Type': 'application/json'}
            })            
            
            .success(function(data, status, headers, config) {
                console.log('success...');
                //$location.path('/takeoutorderview');
            })
            .error(function(data, status, headers, config) {
                 console.log('error...');
            });
        }

    });

    as.controller('TakeoutOrderCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/order',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.orders = data.orders;
                angular.copy($scope.orders, $scope.copy);
            });
    	}
    	
    	load();
    	
        $scope.viewOrderItem = function() {

		}
        
        $scope.cancelOrder = function() {

		}
    });
    
    as.controller('DishCtrl', function($scope, $rootScope, $http, $location) {
        
        var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/dish/10000',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.dishes = data.dishes;
                angular.copy($scope.dishes, $scope.copy);
            });
        };

        load();  
    });

    as.controller('CartCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/cart',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.carts = data.carts;
                angular.copy($scope.carts, $scope.copy);
            });
    	}
    	
    	load();
    	
    });

    as.controller('CartItemCtrl', function($scope, $rootScope, $http, $location) {

    	alert("hi");
    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/cartitem',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.cartitems = data.cartitems;
                angular.copy($scope.cartitems, $scope.copy);
            });
    	}
    	
    	load();
    	
    });
    
    as.controller('OrderCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/order',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.orders = data.orders;
                angular.copy($scope.orders, $scope.copy);
            });
    	}
    	
    	load();
    	
    });
    
    as.controller('OrderItemCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/orderitem',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.orderitems = data.orderitems;
                angular.copy($scope.orderitems, $scope.copy);
            });
    	}
    	
    	load();
    	
    });

    as.controller('ReceiptCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

    	//alert(JSON.stringify($routeParams));
    	var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/customer/order/receipt',
	            method: "POST",
	            data: {'id': 2, 'rid': 10000},
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.orderitems = data.orderitems;
                angular.copy($scope.orderitems, $scope.copy);
            });
    	}
    	
    	load();
    	
    });
}());