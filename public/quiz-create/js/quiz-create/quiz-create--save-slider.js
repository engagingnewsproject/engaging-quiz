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
