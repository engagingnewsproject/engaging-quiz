/*
* What needs to happen on Load
*/

// ready the questions as accordions and add in swanky button template
$('.enp-question-content').each(function(i) {
    // set up accordions
    setUpAccordion($(this));
    // add in image upload button template if it doesn't have an image
    if($('.enp-question-image', this).length === 0) {
        $('.enp-question-image__input', this).after(questionImageUploadButtonTemplate());
    }
});

// hide descriptions
$('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input').hide();

// set-up our ajax response container for messages to get added to
$('#enp-quiz').append('<section class="enp-quiz-message-ajax-container"></section>');

// add our sliders into the templates
$('.enp-slider-options').each(function() {
    // scrape the input values and create the template
    sliderRangeLow = $('.enp-slider-range-low__input', this).val();
    sliderRangeHigh = $('.enp-slider-range-high__input', this).val();
    sliderStart = (sliderRangeHigh - sliderRangeLow)/2;

    slider = sliderTakeTemplate({
            slider_id: $('.enp-slider-id', this).val(),
            slider_range_low: sliderRangeLow,
            slider_range_high: sliderRangeHigh,
            slider_start: sliderStart,
            slider_increment: $('.enp-slider-increment__input', this).val(),
            slider_prefix: $('.enp-slider-prefix__input', this).val(),
            slider_suffix: $('.enp-slider-suffix__input', this).val(),
            slider_input_size: $('.enp-slider-range-high__input', this).val().length
        });

    // insert it
    $(slider).prependTo(this);
    $('.enp-slider__label', this).text('Example Slider');

    // add in the jQuery slider
    $( ".jquery-slider", this ).slider();
});
