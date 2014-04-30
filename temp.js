
function temp() {

	// Create the audio tag
	var soundFile = document.createElement("audio");
	soundFile.preload = "auto";

	// Load the sound file (using a source element for expandability)
	var src = document.createElement("source");
	src.src = "music.mp3";
	soundFile.appendChild(src);

	//Load the audio tag
	//It auto plays as a fallback
	soundFile.load();
	soundFile.volume = 0.500000;
	soundFile.play();

}
window.onload = temp;


// Plays the sound
function play() {

	// Set the current time for the audio file to the beginning
	soundFile.currentTime = 0.01;
	soundFile.volume = volume;

	// Due to a bug in Firefox, the audio needs to be played after a delay
	setTimeout(function(){soundFile.play();},1);
}
