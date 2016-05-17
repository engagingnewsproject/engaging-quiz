function temp_addSlider(questionID) {
    // clone the template
    temp_slider = sliderTemplate({
            question_id: questionID,
            question_position: 'newQuestionPosition',
            slider_id: 'newSliderID',
            slider_range_low: '0',
            slider_range_high: '100',
            slider_correct_low: '50',
            slider_correct_high: '50',
            slider_increment: '1',
            slider_prefix: '',
            slider_suffix: ''
        });

    // insert it
    $(temp_slider).appendTo('#enp-question--'+questionID+' .enp-question');
}

// add MC option ID, question ID, question index, and mc option index
function addSlider(new_sliderID, questionID) {

    new_slider_el = document.querySelector('#enp-question--'+questionID+' .enp-slider-options');

    // find/replace all index attributes (just in the name, but it'll search all attributes)
    findReplaceDomAttributes(new_slider_el, /newQuestionPosition/, getQuestionIndex(questionID));

    findReplaceDomAttributes(new_slider_el, /newSliderID/, new_sliderID);
}



function createSliderTemplate(obj) {
    var sliderData;
    // scrape the input values and create the template
    sliderRangeLow = parseFloat($('.enp-slider-range-low__input', obj).val());
    sliderRangeHigh = parseFloat($('.enp-slider-range-high__input', obj).val());
    sliderIncrement = parseFloat($('.enp-slider-increment__input', obj).val());
    sliderStart = getSliderStart(sliderRangeLow, sliderRangeHigh, sliderIncrement);

    sliderData = {
        'slider_id': $('.enp-slider-id', obj).val(),
        'slider_range_low': sliderRangeLow,
        'slider_range_high': sliderRangeHigh,
        'slider_start': sliderStart,
        'slider_increment': sliderIncrement,
        'slider_prefix': $('.enp-slider-prefix__input', obj).val(),
        'slider_suffix': $('.enp-slider-suffix__input', obj).val(),
        'slider_input_size': $('.enp-slider-range-high__input', obj).val().length
    };

    // create slider template
    slider = sliderTakeTemplate(sliderData);
    sliderExample = $('<div class="enp-slider-preview"></div>').html(slider);

    // insert it
    $(sliderExample).prependTo(obj);
    $('.enp-slider__label', obj).text('Slider Preview');
    // create the jQuery slider
    createSlider($('.enp-slider-input__input', obj), sliderData);
}

// on change slider values
$(document).on('blur', '.enp-slider-range-low__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderRangeLow = parseFloat($(this).val());
    slider.slider('option', 'min',  sliderRangeLow);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('min', sliderRangeLow);

    // set midpoint
    setSliderStart(slider, sliderInput);
});

// on change slider values
$(document).on('input', '.enp-slider-range-low__input', function() {
    var low;
    // get input value
    low = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider_input__range-helper__number--low');
    sliderPreview.text(low);

});

// on change slider values
$(document).on('input', '.enp-slider-range-high__input', function() {
    var high;
    // get input value
    high = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider_input__range-helper__number--high');
    sliderPreview.text(high);
});

// update high range and max value
$(document).on('blur', '.enp-slider-range-high__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderRangeHigh = parseFloat($(this).val());
    slider.slider('option', 'max',  sliderRangeHigh);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('max', sliderRangeHigh);
    // set midpoint
    setSliderStart(slider, sliderInput);
});

// update high range and max value
$(document).on('blur', '.enp-slider-increment__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderIncrement = parseFloat($(this).val());
    slider.slider('option', 'step',  sliderIncrement);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('step', sliderIncrement);

    // set midpoint
    setSliderStart(slider, sliderInput);
});

// update the slider prefix on keydown
$(document).on('keyup', '.enp-slider-prefix__input', function() {
    var prefix;
    // get input value
    prefix = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider-input__prefix');
    sliderPreview.text(prefix);
});

// update the slider suffix on keydown
$(document).on('keyup', '.enp-slider-suffix__input', function() {
    var suffix;
    // get input value
    suffix = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider-input__suffix');
    sliderPreview.text(suffix);
});

// set the slider to the middle point
function setSliderStart(slider, sliderInput) {
    low = slider.slider('option', 'min');
    high = slider.slider('option', 'max');
    interval = slider.slider('option', 'step');
    sliderValue = getSliderStart(low, high, interval);
    // set it
    slider.slider("value", sliderValue);
    sliderInput.val(sliderValue);
}

// calculate where the slider should start
function getSliderStart(low, high, interval) {
    low = parseFloat(low);
    high = parseFloat(high);
    interval = parseFloat(interval);

    totalIntervals = (high - low)/interval;
    middleInterval = ((totalIntervals/2)*interval) + low;
    remainder = middleInterval % interval;
    middleInterval = middleInterval - remainder;

    return middleInterval;
}

function getSliderPreviewElement(obj, element) {
    return obj.closest('.enp-slider-options').find(element);
}
