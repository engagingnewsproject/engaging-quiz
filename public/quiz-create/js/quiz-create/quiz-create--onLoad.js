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
    createSliderTemplate($(this));

    sliderID = $('.enp-slider-id', this).val();
    // add the sliderID to all the inputs
    $('input', this).each(function() {
        $(this).data('sliderID', sliderID);
    });

    // set-up accordion for advanced options
    // create the title and content accordion object so our headings can get created
    accordion = {title: 'Advanced Slider Options', content: $('.enp-slider-advanced-options__content', this), baseID: sliderID};
    //returns an accordion object with the header object and content object
    accordion = enp_accordion__create_headers(accordion);
    // set-up all the accordion classes and start classes (so they're closed by default)
    enp_accordion__setup(accordion);

});
