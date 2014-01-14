
function map_window_load() {
	map_set_initial_coordinates();
	window.ryans_map = map_display_map();
	map_update_coordinates();
}
window.onload = map_window_load;


/* 
 * Get geolocation coordinates
 */
function map_set_initial_coordinates() {

	// Set geo-coordinates
	navigator.geolocation.getCurrentPosition(function(position) {

		// Stick coordinates in variables
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;

		var original_latitude = document.getElementById("origin_latitude").value;
		var original_longitude = document.getElementById("origin_longitude").value;

		document.getElementById("origin_latitude").value = latitude;
		document.getElementById("origin_longitude").value = longitude;
	});
}

/* 
 * Get geolocation coordinates
 */
function map_update_coordinates() {

	// Set geo-coordinates
//	navigator.geolocation.getCurrentPosition(function(position) {
	navigator.geolocation.watchPosition(function(position) {

		// Stick coordinates in variables
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;

		var original_latitude = document.getElementById("origin_latitude").value;
		var original_longitude = document.getElementById("origin_longitude").value;

		if ( original_longitude != longitude ) {
			// Insert values into input fields
			document.getElementById("origin_latitude").value = latitude;
			document.getElementById("origin_longitude").value = longitude;
//		ryans_map.removeLayer(origin_marker);
//L.marker([origin_latitude, origin_longitude]).clearLayers(window.ryans_map);

//	var origin_marker = L.marker([origin_latitude, origin_longitude]).clearLayers(ryans_map);
	window.ryans_map.removeLayer(window.origin_marker);
	window.origin_marker = L.marker([latitude, longitude]).addTo(ryans_map);
//			alert(original_longitude+':'+longitude);
		}

		setTimeout(map_update_coordinates, 20); // you could choose not to continue on failure...
	});
}

// Useful link for teaching objecty stuff for handing Maps ... http://pastebin.com/1ZzH9zHk

function map_display_map() {

	var destination_latitude = parseFloat(document.getElementById("destination_latitude").value);
	var destination_longitude = parseFloat(document.getElementById("destination_longitude").value);
	var origin_latitude = parseFloat(document.getElementById("origin_latitude").value);
	var origin_longitude = parseFloat(document.getElementById("origin_longitude").value);
	var middle_latitude = (origin_latitude+destination_latitude)/2;
	var middle_longitude = (origin_longitude+destination_longitude)/2;

	var ryans_map = L.map('map').setView([middle_latitude, middle_longitude], 7);
//	L.tileLayer('http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png', {
	L.tileLayer('http://localhost/map/tiles/{z}/{x}/{y}.png', {
		maxZoom: 18,
		attribution: 'Ryan was here',
	}).addTo(ryans_map);

	window.origin_marker = L.marker([origin_latitude, origin_longitude]).addTo(ryans_map);
	var destination_marker = L.marker([destination_latitude, destination_longitude]).addTo(ryans_map);

	// Remove origin marker
	document.getElementById('bla').onclick = function(){
		ryans_map.removeLayer(origin_marker);
	}

	return ryans_map;
}
