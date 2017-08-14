(function(angular) {
  'use strict';
  
  var as = angular.module('myApp', ['ngAnimate','ui.bootstrap']);
  
  as.controller('DashboardCtrl', function($scope,$http) {
	  
	  $scope.myInterval = 5000;
	  $scope.noWrapSlides = false;
	  var slides = $scope.slides = [];
	  
	  $scope.addSlide = function() {
	    var newWidth = 600 + slides.length + 1;
	    slides.push({
	      image: '//placekitten.com/' + newWidth + '/300',
	      text: ['More','Extra','Lots of','Surplus'][slides.length % 4] + ' ' +
	        ['Cats', 'Kittys', 'Felines', 'Cutes'][slides.length % 4]
	    });
	  };
	  for (var i=0; i<4; i++) {
	    $scope.addSlide();
	  }
	  
	  
	  
	  $scope.oneAtATime = true;

	  $scope.groups = [
	                   {
	                     title: 'Dynamic Group Header - 1',
	                     content: 'Dynamic Group Body - 1'
	                   },
	                   {
	                     title: 'Dynamic Group Header - 2',
	                     content: 'Dynamic Group Body - 2'
	                   }
	                 ];

	 $scope.items = ['Item 1', 'Item 2', 'Item 3'];
	
	 $scope.addItem = function() {
	   var newItemNo = $scope.items.length + 1;
	   $scope.items.push('Item ' + newItemNo);
	 };
	
	 $scope.status = {
	   isFirstOpen: true,
	   isFirstDisabled: false
	 };
	  
	 
	 	  $scope.today = function() {
		    $scope.dt = new Date();
		  };
		  $scope.today();

		  $scope.clear = function () {
		    $scope.dt = null;
		  };

		  // Disable weekend selection
		  $scope.disabled = function(date, mode) {
		    return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
		  };

		  $scope.toggleMin = function() {
		    $scope.minDate = $scope.minDate ? null : new Date();
		  };
		  $scope.toggleMin();
		  $scope.maxDate = new Date(2020, 5, 22);

		  $scope.open = function($event) {
		    $scope.status.opened = true;
		  };

		  $scope.setDate = function(year, month, day) {
		    $scope.dt = new Date(year, month, day);
		  };

		  $scope.dateOptions = {
		    formatYear: 'yy',
		    startingDay: 1
		  };

		  $scope.format = 'yyyy/MM/dd';

		  $scope.status = {
		    opened: false
		  };
		 		  
		  $scope.mytime = new Date();
		  $scope.mytime.setHours( 9 );
		  $scope.mytime.setMinutes( 0 );
		    
		  $scope.changed = function () {
		    //$log.log('Time changed to: ' + $scope.mytime);
		  };
	  
		  
		  $scope.getLocation = function(val) {
			    return $http.get('//maps.googleapis.com/maps/api/geocode/json', {
			      params: {
			        address: val,
			        sensor: false
			      }
			    }).then(function(response){
			      return response.data.results.map(function(item){
			        return item.formatted_address;
			      });
			    });
			  };
  });  
  
  
})(window.angular);