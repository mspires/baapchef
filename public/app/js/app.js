(function() {

    var     httpHeaders,message;
    
    var as = angular.module('myApp', ['myApp.filters', 'myApp.services', 'myApp.directives', 'myApp.controllers']);

    as.value('version', '1.0.0');

    as.config(function($routeProvider, $httpProvider) {
        $routeProvider
                .when('/takeoutdishes', {templateUrl: 'partials/takeout/dishes.html', controller: 'TakeoutListCtrl'})
                .when('/takeoutorder', {templateUrl: 'partials/takeout/order.html', controller: 'TakeoutOrderCtrl'})
                .when('/dishes', {templateUrl: 'partials/dish/dishes.html', controller: 'DishCtrl'})
                .when('/cart', {templateUrl: 'partials/dish/cart.html', controller: 'CartCtrl'})
                .when('/cartitem', {templateUrl: 'partials/dish/cartitem.html', controller: 'CartItemCtrl'})
                .when('/order', {templateUrl: 'partials/dish/order.html', controller: 'OrderCtrl'})
                .when('/orderitem', {templateUrl: 'partials/dish/orderitem.html', controller: 'OrderItemCtrl'})
                .when('/receipt', {templateUrl: 'partials/order/receipt.html', controller: 'ReceiptCtrl'})
        		.when('/login', {templateUrl: 'partials/auth/login.html', controller: 'LoginCtrl'})
        		.when('/signup', {templateUrl: 'partials/auth/signup.html', controller: 'SignupCtrl'})
                .otherwise({redirectTo: '/'});
    });

/*    as.config(function($httpProvider) {


        //configure $http to catch message responses and show them
        $httpProvider.responseInterceptors.push(
                function($q) {
                    console.log('call response interceptor and set message...');
                    var setMessage = function(response) {
                        //if the response has a text and a type property, it is a message to be shown
                        //console.log('@data'+response.data);
                        if (response.data.message) {
                            message = {
                                text: response.data.message.text,
                                type: response.data.message.type,
                                show: true
                            };
                        }
                    };
                    return function(promise) {
                        return promise.then(
                                //this is called after each successful server request
                                        function(response) {
                                            setMessage(response);
                                            return response;
                                        },
                                        //this is called after each unsuccessful server request
                                                function(response) {
                                                    setMessage(response);
                                                    return $q.reject(response);
                                                }
                                        );
                                    };
                        });                      
            });*/

        }());
