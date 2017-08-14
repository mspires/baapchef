(function(angular) {
  'use strict';
  
  var as = angular.module('myApp', ['ngRoute', 'ngAnimate','ui.bootstrap','myApp.controllers']);
    
  as.config(['$routeProvider', '$locationProvider',
             function($routeProvider, $locationProvider) {
			      $routeProvider
					.when('/receipt/:id', {templateUrl: 'partials/order/receipt.html', controller: 'ReceiptCtrl'})
					.when('/pinin', {templateUrl: 'partials/auth/pinin.html', controller: 'PininCtrl'})
					.when('/pinout', {templateUrl: 'partials/auth/pinout.html', controller: 'PinoutCtrl'})
					.when('/menu', {templateUrl: 'partials/menu/menu.html', controller: 'MenuCtrl'})
					.when('/order', {templateUrl: 'partials/order/order.html', controller: 'OrderCtrl'})
					.when('/monitor', {templateUrl: 'partials/monitor/monitor.html', controller: 'MonitorCtrl'})
					.when('/customer', {templateUrl: 'partials/customer/customer.html', controller: 'CustomerCtrl'})
					.otherwise({redirectTo: '/menu'});
			      
			      //$locationProvider.html5Mode(true);
  			}
  ]);
  
  
  as.controller('AppCtrl', ['$scope', '$rootScope', '$http', '$location',
        function($scope, $rootScope, $http, $location) {
      
				$scope.activeWhen = function(value) {
					return value ? 'active' : '';
				};
				
				$scope.path = function() {
					return $location.url();
				};
							      
				$rootScope.appUrl = "http://www.baapchef.com";
  		}
  ]);  

  as.controller('MenuCtrl', function($scope, $rootScope, $filter, $http, $location, $interval, $uibModal, $log) {
      
      $scope.filters = {};
      $scope.restaurant = {};
      $scope.user = {};
      $scope.selectedgroup = [];
      $scope.selectedorder = [];
      $scope.selectedcustomer = {id: "0", name:"Unknown"};
      $scope.selecteddishes = [];
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
          	
          	$scope.restaurant = data.restaurant;
          	$scope.user = data.user;
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
  	
      /*start alert message*/
      
      $scope.alerts = [];

      $scope.addAlert = function() {
    	  $scope.alerts.push({msg: 'Another alert!'});
      };

      $scope.closeAlert = function(index) {
    	  $scope.alerts.splice(index, 1);
      };
  	
      $interval(function(){
    	  //$scope.alertchecker();
      },60000);
   
   
      $scope.alertchecker = function() {

     	$http({
            url: $rootScope.appUrl + '/restaurant/order',
            method: "POST",
            headers: {
            	'Content-Type': 'application/json'}
        })            
        .success(function(data, status, headers, config) {
        })
        .error(function(data, status, headers, config) {
        });
     	
	    if($scope.alerts.length >= 0) {
		   $scope.alerts.splice(0, 1);
	    }
      }
      /*alert message end*/
      
    /*  
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
  	*/
      
      $scope.selectdish = function (dishid) {
      
      	var dish = getById($scope.dishes, dishid);

      	
      	$scope.selecteddishes.push(dish);
          
      	$scope.subtotal = parseFloat($scope.subtotal) + parseFloat(dish.price);
      	$scope.tax = $scope.subtotal * $scope.unittax;
      	$scope.total = $scope.subtotal + $scope.tax;
      }

      $scope.deletedish = function (dishid) {
          
			var dish = getById($scope.selecteddishes, dishid);
			var index = $scope.selecteddishes.indexOf(dish);
			$scope.selecteddishes.splice(index, 1); 
			
			$scope.subtotal = parseFloat($scope.subtotal) - parseFloat(dish.price);
      	$scope.tax = $scope.subtotal * 0.097;
      	$scope.total = $scope.subtotal + $scope.tax;
      }

      $scope.makeOrder = function() {
        	
    	  	if($scope.selecteddishes.length > 0) {
    	  
    	  		if(confirm('Are you sure to order now?') == true) {
    	  		
		        	$http({
		                url: $rootScope.appUrl + '/restaurant/order/ordernow',
		                method: "POST",
		                data: { orderid : $scope.selectedorder.id, customer : $scope.selectedcustomer, orderItems : $scope.selecteddishes },
		                headers: {
		                	'Content-Type': 'application/json'}
		            })            
		            
		            .success(function(data, status, headers, config) {
		    
		          	  $scope.selectedorder = data.order;
		            })
		            .error(function(data, status, headers, config) {
		            
		            });
    	  		}
	        }
    	  	else
    	  	{
    	  		alert('No dishes are selected now.');
    	  	}
      }      
      
      $scope.clearOrder = function() {
      	
          $scope.selectedgroup = [];
          $scope.selectedorder = [];
          $scope.selectedcustomer = {id: "0", name:"Unknown"};
          $scope.selecteddishes = [];
          $scope.unittax = 0.0;
          $scope.subtotal = 0.0;
          $scope.tax = 0.0;
          $scope.total = 0.0;
         
      }

      $scope.animationsEnabled = true;

      $scope.toggleAnimation = function () {
          $scope.animationsEnabled = !$scope.animationsEnabled;
      };
      
      
      /* start pinin dialog */

      $scope.openPIN = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          templateUrl: 'pininModalContent.html',
          controller: 'PininModalInstanceCtrl',
          backdrop: 'static',
          size: size,
          resolve: {
            pin: function () {
              return $scope.user;
            }
          }
        });

        modalInstance.result.then(function (pin) {}, function () {
        });
      };

      /* end pinin dialog */
      
        
      /* start customer dialog */
      $scope.openCustomer = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          templateUrl: 'customerModalContent.html',
          controller: 'customerModalInstanceCtrl',
          size: size,
          resolve: {
            items: function () {
              return $scope.customers;
            },
            selectedcustomer: function () {
                return $scope.selectedcustomer;
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
        	$scope.selectedcustomer = selectedItem;
        }, function () {
        	$log.info('Modal dismissed at: ' + new Date());
        });
      };

      /* end dish group dialog */
      
      /* start dish group dialog */
      $scope.openDG = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          templateUrl: 'dgModalContent.html',
          controller: 'dgModalInstanceCtrl',
          size: size,
          resolve: {
            items: function () {
              return $scope.dishgroups;
            },
        	filters: function () {
            return $scope.filters;
        	}
          }
        });

        modalInstance.result.then(function (selectedItem) {
        	$scope.selectedgroup = selectedItem;
        	$scope.filters.groupid = selectedItem.id;
        }, function () {
        	$log.info('Modal dismissed at: ' + new Date());
        });
      };
      /* end dish group dialog */

      /* start order dialog */
      $scope.openOrder = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          templateUrl: 'orderModalContent.html',
          controller: 'orderModalInstanceCtrl',
          size: size,
          resolve: {
            items: function () {
              return $scope.orders;
            },
            restaurant: function () {
                return $scope.restaurant;
            }
          }
        });

        modalInstance.result.then(function (selectedorder) {
        	
        	$scope.selectedorder = selectedorder;
        	$scope.selectedcustomer =  getById($scope.customers,selectedorder.cid);
        	$scope.selecteddishes = [];
        	angular.forEach(selectedorder.orderitems, function(value, key){
        		$scope.selectdish(value.dishid);
        	});
        	
        }, function () {
        	$log.info('Modal dismissed at: ' + new Date());
        });
      };
      /* end order dialog */

      /* start receipt dialog */
      $scope.openReceipt = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          //templateUrl: 'receiptModalContent.html',
          templateUrl: 'partials/order/receipt.html',
          controller: 'receiptModalInstanceCtrl',
          size: size,
          resolve: {
        	order: function () {
              return $scope.order;
            },
            restaurant: function () {
                return $scope.restaurant;
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
        	
        }, function () {
        	$log.info('Modal dismissed at: ' + new Date());
        });
      };
      /* end order dialog */
      
  });

  as.controller('PininModalInstanceCtrl', function ($scope, $uibModalInstance, pin) {

	  $scope.pin = pin;
	  $scope.pin0 = "";
	  $scope.pin1 = "";
	  $scope.pin2 = "";
	  $scope.pin3 = "";
	  	  
	  $scope.enterPIN = function (key) {
		  
		  if($scope.pin0 == '') {
			  $scope.pin0 = key;
		  }
		  else if($scope.pin1 == '') {
			  $scope.pin1 = key;
		  }
		  else if($scope.pin2 == '') {
			  $scope.pin2 = key;
		  }
		  else {
			  $scope.pin3 = key;
		  }
	  }
	  
	  $scope.clearPIN = function () {
		  
		  $scope.pin = "";
		  $scope.pin0 = "";
		  $scope.pin1 = "";
		  $scope.pin2 = "";
		  $scope.pin3 = "";
	  }
	  
	  $scope.ok = function () {
		var pin = $scope.pin0 + $scope.pin1 + $scope.pin2 + $scope.pin3;
	    alert(pin);
		if($scope.pin.password === pin) {
			$uibModalInstance.close($scope.pin);
		}
	  };

	  $scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	  };
  });

  as.controller('customerModalInstanceCtrl', function ($scope, $filter, $uibModalInstance, items) {

	  $scope.search = "";
	  $scope.items = items;
	  $scope.selected = {
	    item: $scope.items[0]
	  };

	  $scope.ok = function () {
	    $uibModalInstance.close($scope.selected.item);
	  };

	  $scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	  };
  });
  
  as.controller('dgModalInstanceCtrl', function ($scope, $uibModalInstance, items) {

	  $scope.items = items;
	  $scope.selected = {
	    item: $scope.items[0]
	  };

	  $scope.ok = function () {
	    $uibModalInstance.close($scope.selected.item);
	  };

	  $scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	  };
  });  

  
  as.controller('receiptModalInstanceCtrl', function ($scope, $rootScope, $http, $uibModalInstance, order, restaurant) {

	  	$scope.restaurant = restaurant;
	  	$scope.order = order;
	  	$scope.orderitems = order.orderitems;
	  	
	  	$scope.ok = function () {
	  		$uibModalInstance.close();
	  	};

	  	$scope.cancel = function () {
	  		$uibModalInstance.dismiss('cancel');
	  	};
	  
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
  
  as.controller('orderModalInstanceCtrl', function ($scope, $rootScope, $http, $uibModal, $uibModalInstance, restaurant) {

	  $scope.restaurant = restaurant;
	  $scope.orders = [];
	  $scope.selectedorder = [];
	  
	  
	  var load = function() {
	    	$http({
	            url: $rootScope.appUrl + '/restaurant/order/list',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'}
	        }) 
	        .success(function(data, status, headers, config) {
	
	            $scope.orders = data.orders;
	            $scope.selectedorder = data.orders[0];
        });
	  }
	
	  load();	  

	  
      /* start receipt dialog */
      $scope.openReceipt = function (size) {

        var modalInstance = $uibModal.open({
          animation: $scope.animationsEnabled,
          templateUrl: 'partials/menu/receipt.html',
          controller: 'receiptModalInstanceCtrl',
          size: size,
          resolve: {
            order: function () {
              return $scope.selectedorder;
            },
            restaurant: function () {
                return $scope.restaurant;
            }
          }
        });

        modalInstance.result.then(function (selectedorder) {
      
        	alert(selectedorder.id);
        }, function () {

        });
      };
      /* end order dialog */	  
	  
      $scope.receiptOrder = function(id) {
      
    	  $scope.selectedorder = getById($scope.orders, id); 
    	  $scope.openReceipt();
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
	        	var order = getById($scope.orders, id)
	        	
	        	order.rstate = 'incook';
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

	  $scope.ok = function (id) {
		  var order = getById($scope.orders, id);
		  $uibModalInstance.close(order);
	  };

	  $scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	  };
  });  
  
  function getById(arr, id) {
      for (var d = 0, len = arr.length; d < len; d += 1) {
          if (arr[d].id === id) {
              return arr[d];
          }
      }
  }  
  
})(window.angular);