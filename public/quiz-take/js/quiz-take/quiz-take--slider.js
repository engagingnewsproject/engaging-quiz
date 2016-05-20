function bindSliderData(questionJSON) {
    // assigns data and creates the jQuery slider
    question = $('#question_'+questionJSON.question_id);
    sliderInput = $('.enp-slider-input__input', question);
    // bind slider JSON data
    sliderInput.data('sliderJSON', questionJSON.slider);
    // create the jQuery slider
    createSlider(sliderInput, questionJSON.slider);
}

/**
* Creates a jQuery slider and injects it after the parent wrapper of the enp slider input
* @param sliderInput $('.enp-slider-input')
* @param sliderData {'sliderID': ID,
*                   'sliderStart': int,
*                   'sliderRangeLow': int,
*                   'sliderRangeHigh': int,
*                   'sliderIncrement': int
*                   }
*/
function createSlider(sliderInput, sliderData) {
    // create the div
    slider = $('<div class="enp-slider" aria-hidden="true" role="presentation"></div>');
    // add data
    slider.data('sliderID', sliderData.slider_id);
    $(sliderInput).data('sliderID', sliderData.slider_id);
    // create the jquery slider
    $(slider).slider({
        range: "min",
        value: parseFloat(sliderData.slider_start),
        min: parseFloat(sliderData.slider_range_low),
        max: parseFloat(sliderData.slider_range_high),
        step: parseFloat(sliderData.slider_increment),
        slide: function( event, ui ) {
            $( sliderInput ).val( ui.value );
        }
    });
    // get the slider input container
    sliderInputContainer = $(sliderInput).parent();
    // inject the slider to the DOM after the parent wrapper of the input
    sliderInputContainer.after(slider);

    // get the slider range helper template
    sliderTakeRangeHelpers = sliderRangeHelpersTemplate({
            'slider_range_low': parseFloat(sliderData.slider_range_low),
            'slider_range_high': parseFloat(sliderData.slider_range_high)
    });
    // add in the slider range helpers
    sliderInputContainer.append(sliderTakeRangeHelpers);
}

// update slider on value change
$(document).on('input', '.enp-slider-input__input', function(){
    var slider,
        sliderID,
        inputVal;

    // get the ID
    sliderID = $(this).data('sliderID');
    // get the Slider
    slider = getSlider(sliderID);
    // get the input value
    inputVal = $(this).val();
    // update the slider value
    slider.slider("value", inputVal);
    // check to see if the slider value matches the input value
    // the slider will not allow a min/max value outside the allowed slider min/max
    sliderVal = slider.slider("value");
});

// when leaving focus of the input, check to see if it's a valid entry
// and change it if it isn't
$(document).on('blur', '.enp-slider-input__input', function(){
    var slider,
        sliderID,
        inputVal,
        sliderVal;

    // input
    input = $(this);
    // get the ID
    sliderID = $(this).data('sliderID');
    // get the Slider
    slider = getSlider(sliderID);
    // get the input value
    inputVal = input.val();
    // get the slider value
    sliderVal = slider.slider("value");
    // compare the slider and input values
    if(parseInt(sliderVal) !== parseInt(inputVal)) {
        // if they don't match, then then input value is invalid
        // because the jQuery slider won't set the slider value to be outside the
        // accepted min/max range
        // Set the input to the slider value
        input.val(sliderVal);
        // flash red animation to show that we changed it
        input.addClass('enp-slider-input__input--invalid-animation');
        // remove the invalid class after half of a second
        setTimeout(
            function() {
                input.removeClass('enp-slider-input__input--invalid-animation');
            },
            500
        );

    }
});


function getSlider(sliderID, callback) {
    var slider;
    // find the slider with the slider ID that matches
    $('.enp-slider').each(function() {
        // check it's sliderID
        if($(this).data('sliderID') === sliderID) {
            // if it equals, then set the slider var and break out of the each loop
            slider = $(this);
            if(typeof(callback) == "function") {
                callback($(this));
            }
            return false;
        }
    });
    return slider;
}

function buildSlider(questionJSON) {
    sliderJSON = questionJSON.slider;
    sliderData = {
                    slider_id: sliderJSON.slider_id,
                    slider_range_low: sliderJSON.slider_range_low,
                    slider_range_high: sliderJSON.slider_range_high,
                    slider_correct_low: sliderJSON.slider_correct_low,
                    slider_correct_high: sliderJSON.slider_correct_high,
                    slider_increment: sliderJSON.slider_increment,
                    slider_start: sliderJSON.slider_start,
                    slider_prefix: sliderJSON.slider_prefix,
                    slider_suffix: sliderJSON.slider_suffix,
                    slider_input_size: sliderJSON.slider_range_high.length
                };
    // generate slider template
    slider = sliderTemplate(sliderData);
    // inject the slider template into the page
    $('#question_'+questionJSON.question_id+' .enp-question__submit').before(slider);
    // bind the data to the slider
    bindSliderData(questionJSON);
}
