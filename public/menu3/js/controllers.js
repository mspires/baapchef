(function() {
    var as = angular.module('myApp.controllers', []);

    as.controller('AppCtrl', function($scope, $rootScope, $http, $location) {
        
    	$scope.activeWhen = function(value) {
            return value ? 'active' : '';
        };

        $scope.path = function() {
            return $location.url();
        };

        $rootScope.appUrl = "http://www.baapchef.com";
    });

    as.controller('CustomerCtrl', function($scope, $rootScope, $http) {
        
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

    as.controller('PinoutCtrl', function($scope, $rootScope, $http, $location) {

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

    as.controller('PininCtrl', function($scope, $rootScope, $http, $location) {

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
    
    as.controller('MonitorCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

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
    
    as.controller('OrderCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

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
        
        $scope.readyOrder = function() {
 
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

    as.controller('ReceiptCtrl', function($scope, $rootScope, $http, $routeParams, $location) {

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
    
    as.controller('MenuCtrl', function($scope, $rootScope, $filter, $http, $location) {
        
        $scope.filters = {};
        $scope.selectdishes = [];
        $scope.unittax = 0.0;
        $scope.subtotal = 0.0;
        $scope.tax = 0.0;
        $scope.total = 0.0;
        
        $scope.groupBoxflag = true;
        $scope.customerBoxflag = true;
        
    	var load = function() {
            //console.log('call load()...');
            $http({
                url: $rootScope.appUrl + '/restaurant/dish/menu',
                method: "POST",
                headers: {
                	'Content-Type': 'application/json'}
            })
            .success(function(data, status, headers, config) {
    	
            	$scope.unittax = parseFloat(data.restaurant.tax) / 100;
            	
            	$scope.dishes = data.dishes;
            	$scope.dishgroups = data.dishgroups;
            });
            
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
    	
    	$scope.showGroup = function() {
    		
    		return $scope.groupBoxflag;
    	}
    	
    	$scope.showGroupBox = function(flag) {
    		
    		$scope.groupBoxflag = flag;
    	}
    	
    	$scope.showCustomer = function() {
    		
    		return $scope.customerBoxflag;
    	}
    	
    	$scope.showCustomerBox = function(flag) {
    		
    		$scope.customerBoxflag = flag;
    	}
    	
        $scope.selectdish = function (dishid) {
        
        	var dish = getById($scope.dishes, dishid);
 
        	
        	$scope.selectdishes.push(dish);
            
        	$scope.subtotal = parseFloat($scope.subtotal) + parseFloat(dish.price);
        	$scope.tax = $scope.subtotal * $scope.unittax;
        	$scope.total = $scope.subtotal + $scope.tax;
        }

        $scope.deletedish = function (dishid) {
            
			var dish = getById($scope.selectdishes, dishid);
			var index = $scope.selectdishes.indexOf(dish);
			$scope.selectdishes.splice(index, 1); 
			
			$scope.subtotal = parseFloat($scope.subtotal) - parseFloat(dish.price);
        	$scope.tax = $scope.subtotal * 0.097;
        	$scope.total = $scope.subtotal + $scope.tax;
        }
        
        $scope.makeOrder = function() {
        	
        	$http({
                url: $rootScope.appUrl + '/restaurant/order',
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
    
    function getById(arr, id) {
        for (var d = 0, len = arr.length; d < len; d += 1) {
            if (arr[d].id === id) {
                return arr[d];
            }
        }
    }
    
}());