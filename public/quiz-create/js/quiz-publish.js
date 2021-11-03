jQuery(document).ready(function ($) {
	// select all text on focus
	$(".enp-embed-code").focus(function () {
		$(this).select();
	});

	// unset/reset localStorage quiz values
});
// simple js modal
// https://thecodingpie.com/post/how-to-make-a-simple-modal-popup-box-with-css-and-javascript
// select the open-btn button
let openBtn = document.getElementById("enp-open__btn");
// select the modal-background
let modalBackground = document.getElementById("enp-modal__background");
// select the close-btn
let closeBtn = document.getElementById("enp-close__btn");

// shows the modal when the user clicks open-btn
openBtn.addEventListener("click", function () {
	modalBackground.style.display = "block";
});

// hides the modal when the user clicks close-btn
closeBtn.addEventListener("click", function () {
	modalBackground.style.display = "none";
});

// hides the modal when the user clicks outside the modal
window.addEventListener("click", function (event) {
	// check if the event happened on the modal-background
	if (event.target === modalBackground) {
		// hides the modal
		modalBackground.style.display = "none";
	}
});
