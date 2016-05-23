/**
* Submit question when you click question input label
*
* Process: On click of a selection,
* 1. Show the right one
* 2. Slide out incorrect ones (other than the one clicked)
* 3. Show the explanation
* 4. Trigger click on the question submit button
*/
$(document).on('click', '.enp-option__label', function(e){
    // make sure the question hasn't already been answered
    if(!$('.enp-question__container--unanswered').length) {
        return false;
    }
    // get the input related to the label
    var thisMCInput = $(this).prev('.enp-option__input');
    // See if the DOM has updated to select the corresponding input yet or not.
    // if it hasn't select it, then submit the form
    if ( !thisMCInput.prop( "checked" ) ) {
        thisMCInput.prop("checked", true);
    }

    // just trigger a click on the submit button
    $('.enp-question__submit').trigger('click');
});


// 5. save the quiz on click
// AJAX save
$(document).on('click', '.enp-question__submit', function(e){
    e.preventDefault();
    // set-up the current question container class state, remove unanswered class
    $('.enp-question__container').addClass('enp-question__container--explanation').removeClass('enp-question__container--unanswered');

    // get the JSON data for this question
    var questionJSON = $(this).closest('.enp-question__fieldset').data('questionJSON');

    // if mc option
    if(questionJSON.question_type === 'mc') {
        // find the selected mc option input
        var selectedMCInput = $('.enp-option__input:checked');
        // see if the input is correct or incorrect
        var correct = selectedMCInput.data('correct');

        // check if it's correct or not
        if(correct === '1') {
            correct_string = 'correct';
            // it's right! add the correct class to the input
            selectedMCInput.addClass('enp-option__input--correct-clicked');
            // add the class thta highlights the correct option
            showCorrectMCOption(selectedMCInput);
        } else {
            // it's wrong :( :( :(
            correct_string = 'incorrect';
            // add incorrect clicked class so it remains in view, but is highlighted as the one they clicked
            selectedMCInput.addClass('enp-option__input--incorrect-clicked');
            // highlight the correct option
            correctInput = locateCorrectMCOption($('.enp-question__fieldset'), showCorrectMCOption);
        }
        // remove all the ones that are incorrect that DON'T Have incorrect-clicked on them
        locateIncorrectMCOptions($('.enp-question__fieldset'), removeMCOption);
    } else if(questionJSON.question_type === 'slider') {
        // get the slider input
        sliderInput = $(this).parent().find('.enp-slider-input__input');

        //
        // TODO: disable the sliderInput
        //

        // get the value they entered in the slider input
        sliderSubmittedVal = parseFloat(sliderInput.val());
        // get sliderJSON
        sliderJSON = sliderInput.data('sliderJSON');
        sliderCorrectLow = parseFloat(sliderJSON.slider_correct_low);
        sliderCorrectHigh = parseFloat(sliderJSON.slider_correct_high);
        questionFieldset = $(this).parent();
        // see if it's correct
        if(sliderCorrectLow <= sliderSubmittedVal && sliderSubmittedVal <= sliderCorrectHigh) {
            // correct!
            correct_string = 'correct';
        } else {
            // wrong!
            correct_string = 'incorrect';
        }

        sliderInput.addClass('enp-slider-input__input--'+correct_string);
        $('.ui-slider-range-min', questionFieldset).addClass('ui-slider-range-min--'+correct_string);
        $('.ui-slider-handle', questionFieldset).addClass('ui-slider-handle--'+correct_string);

        if(correct_string === 'incorrect' || sliderCorrectLow !== sliderCorrectHigh) {
            // fade out range helpers in case the answer is at the very end or beginning. // If it is at the beg/end, then it'll overlap in a weird way
            $('.enp-slider-input__range-helper__number').hide();

            $('.ui-slider-range-min', questionFieldset).after('<div class="ui-slider-range-show-correct ui-slider-range"></div>');
            // figure out how to overlay some kind of red bar on top of the slider
            // and display the right values
            // calulate total intervals
            sliderRangeLow = parseFloat(sliderJSON.slider_range_low);
            sliderRangeHigh = parseFloat(sliderJSON.slider_range_high);
            sliderIncrement = parseFloat(sliderJSON.slider_increment);
            sliderTotalIntervals = (sliderRangeHigh - sliderRangeLow)/sliderIncrement;
            // calculate offset left for answer
            // how many intervals until the right answer?
            correctLowIntervals = sliderCorrectLow/sliderIncrement;
            correctHighIntervals = sliderCorrectHigh/sliderIncrement;
            // what percentage offset should it be?
            correctLowOffsetLeft = (correctLowIntervals/sliderTotalIntervals) * 100;
            correctHighOffsetLeft = (correctHighIntervals/sliderTotalIntervals) * 100;
            // calculate width for answer in % (default 1% if equal low/high)
            correctRangeWidth = correctHighOffsetLeft - correctLowOffsetLeft;

            // set the attributes on our correct width bar
            $('.ui-slider-range-show-correct', questionFieldset).css({'width': correctRangeWidth+'%','left':correctLowOffsetLeft+'%'}).text(sliderJSON.sliderCorrectLow);
            toolTipHTML = '<div class="ui-slider-range-show-correct__tooltip ui-slider-range-show-correct__tooltip--low"><span class="ui-slider-range-show-correct__tooltip__text ui-slider-range-show-correct__tooltip__text--low">'+sliderCorrectLow+'</span></div>';
            if(sliderCorrectLow !== sliderCorrectHigh) {
                toolTipHTML += '<div class="ui-slider-range-show-correct__tooltip ui-slider-range-show-correct__tooltip--high"><span class="ui-slider-range-show-correct__tooltip__text ui-slider-range-show-correct__tooltip__text--high">'+sliderCorrectHigh+'</span></div>';
            }

            // add in a tool tip to display the correct answer
            $('.ui-slider-range-show-correct', questionFieldset).append('<div class="ui-slider-range-show-correct__tooltip-container">'+toolTipHTML+'</div>');
            // center the correct indicator label if they match
            if(sliderCorrectLow === sliderCorrectHigh) {
                $('.ui-slider-range-show-correct__tooltip__text--low', questionFieldset).addClass('ui-slider-range-show-correct__tooltip__text--low-center');
            }
        }

    }

    // add answered class
    $(this).closest('.enp-question__fieldset').addClass('enp-question__answered');
    // show the explanation by generating the question explanation template
    var qExplanationTemplate = generateQuestionExplanation(questionJSON, correct_string);

    // add the Question Explanation Template into the DOM
    $('.enp-question__submit').before(qExplanationTemplate);
    // submit the question
    data = prepareQuestionFormData($(this));
    url = $('.enp-question__form').attr('action');

    // AJAX Submit form
    $.ajax( {
        type: 'POST',
        url  : url,
        data : data,
        dataType : 'json',
    } )
    // success
    .done( questionSaveSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {
        console.log( 'AJAX after finished' );
    } )
    .always(function() {

    });
});

function questionSaveSuccess( response, textStatus, jqXHR ) {
    // real quick, remove the submit button so it can't get submitted again
    $('.enp-question__submit').remove();
    // get the response
    var responseJSON = $.parseJSON(jqXHR.responseText);
    console.log(responseJSON);
    // see if there's a next question
    if(responseJSON.next_state === 'question') {
        // we have a next question, so generate it
        generateQuestion(responseJSON.next_question);
    } else {
        // we're at the quiz end, in the future, we might get some data
        // ready so we can populate quiz end instantly. Let's just do it based on a response from the server instead for now so we don't have to set localStorage and have duplicate copy for all the quiz end states

    }

    // send the height of the new view
    sendBodyHeight();

}

/**
* Binds JSON data to the main question element in the DOM so we always have
* access to it. Accessible via
* $('#question_'+questionJSON.question_id).data('questionJSON');
*/
function bindQuestionData(questionJSON) {
    $('#question_'+questionJSON.question_id).data('questionJSON', questionJSON);
}

/**
* Shortcut function for getting JSON data from the question wrapper element.
* Not super necessary, but if we ever want to filter/change the data before
* sending the data back, this would be handy.
* @param questionID (int/string) question Id of the
*        question in the DOM you want data for
* @return JSON data for the question
*/
function getQuestionData(questionID) {
    return $('#question_'+questionID).data('questionJSON', questionJSON);
}

/**
* Generates a new Question off of Question JSON data and the Question Template(s)
* and inserts it into the page as an "on-deck" question
*/
function generateQuestion(questionJSON) {

    var questionData = {
                        'question_id': questionJSON.question_id,
                        'question_type': questionJSON.question_type,
                        'question_title': questionJSON.question_title,
    };

    new_questionTemplate = questionTemplate(questionData);
    $('.enp-question__fieldset').before(new_questionTemplate);
    // find it and add the classes we need
    $('#question_'+questionJSON.question_id).addClass('enp-question--on-deck');
    // add the data to the new question
    bindQuestionData(questionJSON);

    // add in the image template, if necessary
    if(questionJSON.question_image !== '') {
        buildImageTemplate(questionJSON);
    }

    // Build templates and bind data for the question
    if(questionJSON.question_type === 'mc') {
        // build mc option templates and bind data
        buildMCOptions(questionJSON);
    } else if(questionJSON.question_type === 'slider') {
        // build slider template and bind data
        buildSlider(questionJSON);
    }
}

/**
* Increase the current question number on the progress bar
* and the width of the progress bar
* @param questionOrder = the question_order of the next question
*/
function increaseQuestionProgress(questionOrder) {
    questionNumber = parseInt(questionOrder) + 1;
    // increase the question number and css if we have another one
    totalQuestions = _.get_total_questions();
    progressBarWidth = (questionNumber/totalQuestions) * 100;
    if(progressBarWidth === 100) {
        progressBarWidth = 95;
    }
    progressBarWidth = progressBarWidth + '%';

    // BEM Taken WAAAAAAY too far...
    $('.enp-quiz__progress__bar__question-count__current-number').text(questionNumber);
    $('.enp-quiz__progress__bar').css('width', progressBarWidth);
}


/**
* Add/Remove classes to bring in the next question
*/
function showNextQuestion(obj) {
    obj.addClass('enp-question--show').removeClass('enp-question--on-deck');
    // get the data from it
    questionShowJSON = obj.data('questionJSON');
    questionOrder = questionShowJSON.question_order;
    // increase the number and the width of the progress bar
    increaseQuestionProgress(questionOrder);
}


/**
* Prepare the form data for submitting via AJAX
*
*/
function prepareQuestionFormData(clickedButton) {
    // add button value and name to the data since jQuery doesn't submit button value
    userAction = clickedButton.attr("name") + "=" + clickedButton.val();
    // add in a little data to let the server know the data is coming from an ajax call
    doing_ajax = 'doing_ajax=doing_ajax';
    data = $('.enp-question__form').serialize() + "&" + userAction + "&" + doing_ajax;

    return data;
}

function buildImageTemplate(questionJSON) {
    // get the template and add it in
    var questionImageData = {
                        'question_image_src': questionJSON.question_image_src,
                        'question_image_srcset': questionJSON.question_image_srcset,
                        'question_image_alt': questionJSON.question_image_alt,
    };
    // populate the template
    new_questionImageTemplate = questionImageTemplate(questionImageData);
    // insert it into the page
    $('#question_'+questionJSON.question_id+' .enp-question__question').after(new_questionImageTemplate);
}
