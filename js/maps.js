// Found http://blog.sofasurfer.org/2011/06/27/dynamic-google-map-markers-via-simple-json-file/
// data file with markers (could also be a PHP file for dynamic markers)
	var newDate = new Date;				
	var markerFile = 'markers.json';
 
	// set default map properties
	var defaultLatlng = new google.maps.LatLng(0.0,0.0);
	
	// zoom level of the map		
	var defaultZoom = 3;
	
	// variable for map
	var map;
	
	var polylines = new Array();
	
	// variable for marker info window
	var infowindow;
 
	// List with all marker to check if exist
	var markerList = {};
 
	// set error handler for jQuery AJAX requests
	$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
		alert(textStatus);
		alert(errorThrown);
		alert(XMLHttpRequest.responseText);
	}});

	// option for google map object
	var myOptions = {
		zoom: defaultZoom,
		center: defaultLatlng,
		mapTypeId: google.maps.MapTypeId.HYBRID
	}


	/**
	 * Load Map
	 */
	function loadMap(){

		// create new map make sure a DIV with id myMap exist on page
		map = new google.maps.Map(document.getElementById("myMap"), myOptions);

		// create new info window for marker detail pop-up
		infowindow = new google.maps.InfoWindow();
		
		// load markers
		loadMarkers();
	}
 
 
	/**
	 * Load markers via ajax request from server
	 */
	function loadMarkers(){
 
 		// load marker jSon data		
		$.getJSON(markerFile, function(data) {
			
			// loop all the markers
			$.each(data.markers, function(i,item){
				
				// add marker to map
				loadMarker(item);

			    var flightPlanCoordinates = [               
			            new google.maps.LatLng(48.2084900, 16.3720800),              
			            new google.maps.LatLng(item.lat, item.long)               
			    ];
			    
			    var lineSymbol = {
			    	    path: google.maps.SymbolPath.CIRCLE,
			    	    scale: 2,
			    	    strokeColor: '#393'
			    	  };
			    
			   /*
			    var polyline;   
			    polyline = new google.maps.Polyline({
			        path: flightPlanCoordinates,
			        icons: [{
			            icon: lineSymbol,
			            offset: '100%'
			          }],
			        geodesic: true
			    });
			    */
			    var polyline;   
			    polyline = new google.maps.Polyline({
			        path: flightPlanCoordinates,
			        strokeColor: "#ddd",
			        strokeOpacity: 1.0,
			        strokeWeight: 1,
			        icons: [{
			            icon: lineSymbol,
			            offset: '100%'
			          }],
			        geodesic: true
			    });
			    //new polyline
			    polyline.setMap(map);   
			    // assign to global var path
			    path = polyline;
			    
			    /*
			    var i;
			    for (i = 0; i < 50; i = i + 5) {
			        //unchanged code
			        polylines[i] = path;
			        animateCircle(i);
			    }
			    */
			});
		});
	}
	
	function animateCircle(id) {
	    var count = 0;
	    offsetId = window.setInterval(function () {
	        count = (count + 1) % 200;

	        var icons = polylines[id].get('icons');
	        icons[0].offset = (count / 2) + '%';
	        polylines[id].set('icons', icons);
	    }, 20);
	}

	/**
	 * Load marker to map
	 */
	function loadMarker(markerData){
		
		// create new marker location
		var myLatlng = new google.maps.LatLng(markerData['lat'],markerData['long']);

		// create new marker				
		var marker = new google.maps.Marker({
		    id: markerData['id'],
		    map: map, 
		    title: markerData['ip'] ,
		    position: myLatlng
		});

		// add marker to list used later to get content and additional marker information
		markerList[marker.id] = marker;

		// add event listener when marker is clicked
		// currently the marker data contain a dataurl field this can of course be done different
		google.maps.event.addListener(marker, 'click', function() {
			
			// show marker when clicked
			showMarker(marker.id);
			
			showMarkerFromList(marker.id);

		});

		// add event when marker window is closed to reset map location
		google.maps.event.addListener(infowindow,'closeclick', function() { 
			map.setCenter(defaultLatlng);
			map.setZoom(defaultZoom);
		}); 	
	}	
	
	/**
	 * Show marker info window
	 */
	function showMarker(markerId){
		
		// get marker information from marker list
		var marker = markerList[markerId];
		
		// check if marker was found
		if( marker ){
			// get marker detail information from server
			$.get( 'marker.php?id='+marker.id , function(data) {
				// show marker window
				infowindow.setContent(data);
				infowindow.open(map,marker);
			});	
		}else{
			alert('Error marker not found: ' + markerId);
		}
	}
	
	function showMarkerFromList(markerId){
		var marker = markerList[markerId];
		
		if( marker ){
			// get marker detail information from server
			$.get( 'marker.php?id='+marker.id , function(data) {
				// show marker window
				infowindow.setContent(data);
				infowindow.open(map,marker);
			});	
		}else{
			alert('Error marker not found: ' + markerId);
		}
	}
	 
	/**
	 * Adds new marker to list
	 */
	function newMarker(){
 
		// get new city name
		var markerAddress = $('#newMarker').val();
 
		// create new geocoder for dynamic map lookup
		var geocoder = new google.maps.Geocoder();
		
		geocoder.geocode( { 'address': markerAddress}, function(results, status) {
		
			// check response status
			if (status == google.maps.GeocoderStatus.OK) {
				
				// Fire Google Goal 
				_gaq.push(['_trackPageview', '/tracking/marker-submit']);			

				// set new maker id via timestamp
				var newDate = new Date;				
				var markerId = newDate.getTime();
				
				// get name of creator
				var markerCreator = prompt("Please enter your name","");
				
				// create new marker data object
				var markerData = {
					'id': markerId,
					'lat': results[0].geometry.location.lat(),
					'long': results[0].geometry.location.lng(),
					'creator': markerCreator,
					'name': markerAddress,
				};
 
				// save new marker request to server
				$.ajax({
					type: 'POST',			
					url: "data.php",
					data: {
						marker: markerData
					},
					dataType: 'json',
					async: false,
					success: function(result){
						// add marker to map
						loadMarker(result);
												
						// show marker detail
						showMarker(result['id']);
					}
				});
				
			}else if( status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT){
				alert("Marker not found:" + status);
			}
		});
	}
 
$(document).ready(function(){
	$('ul li').click(function(){
		var data = $(this).attr('data-value');
		showMarkerFromList(data);
	});
});