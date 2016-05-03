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
    json = '{"quiz_id":"'+getQuizID()+'","height":"'+height+'"}';
    parent.postMessage(json, "*");
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
    return height + "px";
}

window.addEventListener('message', receiveMessage, false);

function receiveMessage(event) {
    // check to make sure we received a string
    if(typeof event.data !== 'string') {
        return false;
    }
    // If our request isn't our height, send a request for a valid height
    data = JSON.parse(event.data);
    if(!/([0-9]px)/.test(data.height)) {
        sendBodyHeight();
    }

}
