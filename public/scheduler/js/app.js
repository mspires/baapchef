(function(angular) {
  'use strict';
  
  var as = angular.module('myApp', ['ngAnimate','ui.calendar','ui.bootstrap']);
  
  as.controller('CalendarCtrl', function($scope, $http, $compile, $timeout, $uibModal, uiCalendarConfig) {
	  
	$scope.showAddReservation = false;  
	$scope.appUrl = "http://www.baapchef.com";
	  
  	var load = function() {
        console.log('call load()...');
        $http({
            url: $scope.appUrl + '/restaurant/reservation/index',
            method: "POST",
            headers: {
            	'Content-Type': 'application/json'}
        })
        .success(function(data, status, headers, config) {
	
		    $scope.tables = data.tables;
		    $scope.customers = data.customers;
		    $scope.users = data.users;
		    $scope.reservations = data.reservations;
        });
    }

    load();
	  
    
    
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
			  
			  

		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		/* event source that pulls from google.com */
		/*
		$scope.eventSource = {
		        url: "http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic",
		        className: 'gcal-event',           // an option!
		        currentTimezone: 'America/Chicago' // an option!
		};
		*/
		/* event source that contains custom events on the scope */
		$scope.events = [];
		
		/* event source that calls a function on every view switch */		
		$scope.eventsF = function (start, end, timezone, callback) {
		  var s = new Date(start).getTime() / 1000;
		  var e = new Date(end).getTime() / 1000;
		  var m = new Date(start).getMonth();
		  var events = [{title: 'Feed Me ' + m,start: s + (50000),end: s + (100000),allDay: false, className: ['customFeed']}];
		  
		  callback(events);
		};
		
		$scope.calEventsExt = {
		   color: '#f00',
		   textColor: 'yellow',
		   events: []
		};
		/* alert on eventClick */
		$scope.alertOnEventClick = function( date, jsEvent, view){
		    $scope.alertMessage = (date.title + ' was clicked ');
		};
		/* alert on Drop */
		 $scope.alertOnDrop = function(event, delta, revertFunc, jsEvent, ui, view){
		   $scope.alertMessage = ('Event Dropped to make dayDelta ' + delta);
		};
		/* alert on Resize */
		$scope.alertOnResize = function(event, delta, revertFunc, jsEvent, ui, view ){
		   $scope.alertMessage = ('Event Resized to make dayDelta ' + delta);
		};
		/* add and removes an event source of choice */
		$scope.addRemoveEventSource = function(sources,source) {
		  var canAdd = 0;
		  angular.forEach(sources,function(value, key){
		    if(sources[key] === source){
		      sources.splice(key,1);
		      canAdd = 1;
		    }
		  });
		  if(canAdd === 0){
		    sources.push(source);
		  }
		};
		/* add custom event*/
		$scope.addEvent = function() {
			
			var y = $scope.dt.getFullYear();
			var d = $scope.dt.getDate();
			var m = $scope.dt.getMonth();
			var hh = $scope.mytime.getHours();
			var mm = $scope.mytime.getMinutes();
			var duration = 2;
			
	        $http({
	            url: $scope.appUrl + '/restaurant/reservation/add',
	            method: "POST",
	            headers: {
	            	'Content-Type': 'application/json'},
	        	data: {	customer: $scope.selectedcustomer, 
	        			table: $scope.selectedtable,
	        			start: new Date(y, m, d, hh, mm),
	        			end: new Date(y, m, d, hh + duration, mm) 
	            	}
	        })
	        .success(function(data, status, headers, config) {
		
	        });
	        
			$scope.events.push({
				title: 'Reservation ' + ' - ' + $scope.selectedcustomer,
				start: new Date(y, m, d, hh, mm),
				end: new Date(y, m, d, hh + duration, mm),
				className: ['openSesame']
			});
			
		};
		/* remove event */
		$scope.remove = function(index) {
		  $scope.events.splice(index,1);
		};
		/* Change View */
		$scope.changeView = function(view,calendar) {
		  uiCalendarConfig.calendars[calendar].fullCalendar('changeView',view);
		};
		/* Change View */
		$scope.renderCalender = function(calendar) {
		  $timeout(function() {
		    if(uiCalendarConfig.calendars[calendar]){
		      uiCalendarConfig.calendars[calendar].fullCalendar('render');
		    }
		  });
		};
		
		$scope.showTableSchedule = function(id) {
			
			alert(id);
		};
		
		 /* Render Tooltip */
		$scope.eventRender = function( event, element, view ) {
		    //element.attr({'tooltip': event.title,
		    //              'tooltip-append-to-body': true});
		    //$compile(element)($scope);
		};
		
		/* config object */
		$scope.uiConfig = {
		  calendar:{
		    height: 450,
		    editable: true,
		    header:{
		      left: '',
		      center: 'title',
		      right: 'today prev,next'
		    },
		    eventClick: $scope.alertOnEventClick,
		    eventDrop: $scope.alertOnDrop,
		    eventResize: $scope.alertOnResize,
		    eventRender: $scope.eventRender
		  }
		};
		
		/* event sources array*/
		//$scope.eventSources = [$scope.events, $scope.eventSource, $scope.eventsF];
		$scope.eventSources = [$scope.calEventsExt, $scope.eventSource, $scope.eventsF, $scope.events];			  
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
  
})(window.angular);