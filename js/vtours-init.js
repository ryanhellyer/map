
window.mySwipe = Swipe(document.getElementById('location-slider'), {
	auto: false,
	continuous: false,
	/*
	callback: function(pos) {

		var i = bullets.length;
		while (i--) {
			bullets[i].className = ' ';
		}
		bullets[pos].className = 'on';

	}
	*/
});
//var bullets = document.getElementById('position').getElementsByTagName('li');


function menu_window_load() {
	// Create the audio tag
	var soundFile = document.createElement("audio");
	soundFile.preload = "auto";

	// Load the sound file (using a source element for expandability)
	var src = document.createElement("source");
	src.src = "http://localhost/tours/wp-content/themes/virtual-tours/music.mp3";
	soundFile.appendChild(src);

	// Load the audio tag
	// It auto plays as a fallback
	soundFile.load();
	soundFile.volume = 0.500000;

	// Play the audio
	document.getElementById('restart-current-audio').onclick=function(){
		soundFile.currentTime = 0; // Resets it to start
	}

	// Play the audio
	document.getElementById('play-current-audio').onclick=function(){
		var play_button_state = document.getElementById('play-current-audio').className;
		if('stopped'==play_button_state){

			var elements = document.getElementsByClassName('location-slide');

			Array.prototype.forEach.call(elements, function(element) {
				// Do stuff with the element
				style = window.getComputedStyle(element), // Check first
				console.log(style);
				top = style.getPropertyValue('-webkit-transform');
			//	console.log( top);
			//	die;
			//	console.log(element.tagName);
			});

			document.getElementById('play-current-audio').className='playing';

			soundFile.play();

			//Set the current time for the audio file to the beginning
			soundFile.currentTime = 0.01;

//			soundFile.stop();
			//Due to a bug in Firefox, the audio needs to be played after a delay
			setTimeout(function(){soundFile.play();},1);

		} else {
			document.getElementById('play-current-audio').className='stopped';
			soundFile.pause();
			audio.currentTime = 0; // Resets it to start
		}
	}

	// Primary navigation
	document.getElementById('location-slider').onclick=function(){
		document.getElementById('map').className='';
		document.getElementById('main-content').className='';
	}
	document.getElementById('show-menu').onclick=function(){
		document.getElementById('main-menu').className='active';
		document.getElementById('main-content').className='right';
	};
	document.getElementById('main-menu-inner').onclick=function(){
		document.getElementById('main-menu').className='';
		document.getElementById('main-content').className='';
	};

	document.getElementById('show-map').onclick=function(){
		var show_map = document.getElementById('map').className;
		if('active'==show_map){
			document.getElementById('map').className='';
			document.getElementById('main-content').className='';
		} else {
			document.getElementById('map').className='active';
			document.getElementById('main-content').className='left';
		}
	};

	// Iterate through buttons to add links to each slide
	var num_slides = mySwipe.getNumSlides();
	var i = 0;    
	while (i<num_slides) {
		document.getElementById('button-'+i).onclick = (function(i) {

			return function(e) {
				mySwipe.slide(i, 1000);
			}
		})(i);

		i++;
	}

	document.getElementById('next').onclick=function(){
		mySwipe.next();
	}
	document.getElementById('prev').onclick=function(){
		mySwipe.prev();
	}



	// The map
	map_set_initial_coordinates();
	window.ryans_map = map_display_map();
	map_update_coordinates();




	/*
	 * Audio playback
	document.getElementById('play-current-audio').onclick=function(){
		/*
		 * Audio play
		//Create the audio tag
		var soundFile = document.createElement("audio");
		soundFile.preload = "auto";

		//Load the sound file (using a source element for expandability)
		var src = document.createElement("source");
		src.src = vtours_template_uri+"/music.mp3";
		soundFile.appendChild(src);

		//Load the audio tag
		//It auto plays as a fallback
		soundFile.load();
		soundFile.volume = 0.100000;
		soundFile.play();

		//Set the current time for the audio file to the beginning
		soundFile.currentTime = 0.01;
		soundFile.volume = volume;

		//Due to a bug in Firefox, the audio needs to be played after a delay
		setTimeout(function(){soundFile.play();},1);
	}
	 */

}
window.onload = menu_window_load;

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

// Plays the sound
function play() {

	// Set the current time for the audio file to the beginning
	soundFile.currentTime = 0.01;
	soundFile.volume = volume;

	// Due to a bug in Firefox, the audio needs to be played after a delay
	setTimeout(function(){soundFile.play();},1);
}
