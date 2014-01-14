function map_window_load() {
	map_get_coordinates();
	map_display_map();
}

window.onload = map_window_load;


/* 
 * Get geolocation coordinates
 */
function map_get_coordinates() {

	// Watch gelocation (refires whenever location changes)
	navigator.geolocation.watchPosition(function(position) {

		// Stick coordinates in variables
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;

		// Insert values into input fields
		document.getElementById("origin_latitude").value = latitude;
		document.getElementById("origin_longitude").value = longitude;

		var myLatlng = new google.maps.LatLng(origin_latitude,origin_longitude);
		var marker = new google.maps.Marker({
			position: myLatlng,
			title:"The origin!"
		});

		// To add the marker to the map, call setMap();
		marker.setMap(map);


		// Get distance between origin and destination (via Google Maps)
		//map_calculate_distance();

		setTimeout(map_get_coordinates, 2000); // you could choose not to continue on failure...
	});
}


/* 
 * Calculate distance
 *
 * Uses Google maps
 */
function map_calculate_distance() {
	var origin_latitude = document.getElementById("origin_latitude").value;
	var origin_longitude = document.getElementById("origin_longitude").value;

	var destination_latitude = document.getElementById("destination_latitude").value;
	var destination_longitude = document.getElementById("destination_longitude").value;
	var service = new google.maps.DistanceMatrixService();
	service.getDistanceMatrix(
	{
		origins: [origin_latitude+','+origin_longitude],
		destinations: [destination_latitude+','+destination_longitude],
		travelMode: google.maps.TravelMode.WALKING,
		unitSystem: google.maps.UnitSystem.METRIC,
		avoidHighways: false,
		avoidTolls: false
	}, map_calculate_distance_callback);
}

/* 
 * Distance calculation callback
 *
 * Used by map_calculate_distance()
 */
function map_calculate_distance_callback(response,status) {

	if (status == google.maps.DistanceMatrixStatus.OK) {
		var origins = response.originAddresses;
		var destinations = response.destinationAddresses;

		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;
			for (var j = 0; j < results.length; j++) {
				var element = results[j];
				var distance = element.distance.text;
				var duration = element.duration.text;
				var from = origins[i];
				var to = destinations[j];
			}
		}

		// Insert distance into input field
		document.getElementById("distance").value = distance;

	}

}




// Useful link for teaching objecty stuff for handing Maps ... http://pastebin.com/1ZzH9zHk



function map_display_map_initialize() {
	var destination_latitude = document.getElementById("destination_latitude").value;
	var destination_longitude = document.getElementById("destination_longitude").value;

	var myLatlng = new google.maps.LatLng(destination_latitude,destination_longitude);

	var myOptions = {
	zoom: 8,
	center: myLatlng,
	mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById("display_map"), myOptions);

	// To add the marker to the map, use the 'map' property
	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title:"Ma destination, bitch!"
	});

	var origin_latitude = document.getElementById("origin_latitude").value;
	var origin_longitude = document.getElementById("origin_longitude").value;
}

function map_display_map() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=map_display_map_initialize";
	document.body.appendChild(script);
}
