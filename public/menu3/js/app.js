(function() {

    var     httpHeaders,message;
    
    var as = angular.module('myApp', ['myApp.filters', 'myApp.services', 'myApp.directives', 'myApp.controllers']);

    as.value('version', '1.0.0');
    
    as.config(function($routeProvider, $httpProvider) {
        $routeProvider
                .when('/receipt/:id', {templateUrl: 'partials/order/receipt.html', controller: 'ReceiptCtrl'})
        		.when('/pinin', {templateUrl: 'partials/auth/pinin.html', controller: 'PininCtrl'})
        		.when('/pinout', {templateUrl: 'partials/auth/pinout.html', controller: 'PinoutCtrl'})
        		.when('/menu', {templateUrl: 'partials/menu/menu.html', controller: 'MenuCtrl'})
                .when('/order', {templateUrl: 'partials/order/order.html', controller: 'OrderCtrl'})
                .when('/monitor', {templateUrl: 'partials/monitor/monitor.html', controller: 'MonitorCtrl'})
                .when('/customer', {templateUrl: 'partials/customer/customer.html', controller: 'CustomerCtrl'})
        		.otherwise({redirectTo: '/menu'});
    });

    /*
    as.config(function($httpProvider) {


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
            });
	*/
}());
