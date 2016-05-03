/**
* postMessage communication with parent of the iframe
*/

/**
* Sends a postMessage to the parent container of the iframe
*/
function sendBodyHeight() {
    console.log('sending body height');
    // calculate the height
    height = calculateBodyHeight();
    // allow all domains to access this info (*)
    // and send the message to the parent of the iframe
    parent.postMessage(height, "*");
}
/**
* Function for caluting the container height of the iframe
* @return (int)
*/
function calculateBodyHeight() {
    var height = document.getElementById('enp-quiz-container').offsetHeight;

    // calculate the height of the slide-hide mc elements, if there
    if($('.enp-option__input--slide-hide').length) {
        var removedMC = 0;
        $('.enp-option__input--slide-hide').each(function(){
            var label = $(this).next('.enp-option__label');
            removedMC = removedMC + label.outerHeight(true);
        });
        // subtract the height of the removedMC options from the total height
        height = height - removedMC;
    }

    // return the height
    return height;
}

window.addEventListener('message', receiveMessage, false);

function receiveMessage(event) {
    console.log(event.data);
    sendBodyHeight();
}
