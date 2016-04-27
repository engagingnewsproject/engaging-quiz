jQuery( document ).ready( function( $ ) {// UTILITY
/**
* get a string or decimal integer and return a formatted decimal number
* @param places (int) how many decimal places you want to leave in Defaults to 0.
*/
_.reformat_number = function(number, multiplier, places) {
    if(multiplier === "") {
        multiplier = 1;
    }
    if(places === "") {
        places = 0;
    }
    number = number * multiplier;
    number = number.toFixed(places);
    return number;
};

// turn on mustache/handlebars style templating
_.templateSettings = {
  interpolate: /\{\{(.+?)\}\}/g
};
// Templates
var mcOptionTemplate = _.template($('#mc_option_template').html());
var questionTemplate = _.template($('#question_template').html());
var questionImageTemplate = _.template($('#question_image_template').html());
var questionExplanationTemplate = _.template($('#question_explanation_template').html());
var quizEndTemplate = _.template($('#quiz_end_template').html());


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

    // get the JSON data for this question
    var questionJSON = $(this).closest('.enp-question__fieldset').data('questionJSON');
    $(this).closest('.enp-question__fieldset').addClass('enp-question__answered');
    // show the explanation by generating the question explanation template
    var qExplanationTemplate = generateQuestionExplanation(questionJSON, correct_string);
    // add the Question Explanation Template into the DOM
    $('.enp-question__submit').before(qExplanationTemplate);



    // submit the question
    url = $('.enp-question__form').attr('action');
    // add button value and name to the data since jQuery doesn't submit button value
    userAction = $(this).attr("name") + "=" + $(this).val();
    // add in a little data to let the server know the data is coming from an ajax call
    doing_ajax = 'doing_ajax=doing_ajax';
    data = $('.enp-question__form').serialize() + "&" + userAction + "&" + doing_ajax;

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
    var responseJSON = $.parseJSON(jqXHR.responseText);
    // see if there's a next question
    if(responseJSON.next_state === 'question') {
        // we have a next question, so generate it
        generateQuestion(responseJSON.next_question);
    } else {
        // we're at the quiz end, in the future, we might get some data
        // ready so we can populate quiz end instantly. Let's just do it based on a response from the server instead for now so we don't have to set localStorage and have duplicate copy for all the quiz end states

    }

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
                        'question_title': questionJSON.question_title
    };

    new_questionTemplate = questionTemplate(questionData);
    $('.enp-question__fieldset').before(new_questionTemplate);
    // find it and add the classes we need
    $('#question_'+questionJSON.question_id).addClass('enp-question--on-deck');
    // add the data to the new question
    bindQuestionData(questionJSON);

    // add in the image template, if necessary
    if(questionJSON.question_image !== '') {
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

    if(questionJSON.question_type === 'mc') {
        // generate mc option templates
        for(var prop in questionJSON.mc_option) {
            mc_option_id = questionJSON.mc_option[prop].mc_option_id;
            mc_option_content = questionJSON.mc_option[prop].mc_option_content;
            mcOptionData = {
                            'mc_option_id': mc_option_id,
                            'mc_option_content': mc_option_content
            };

            // generate the template
            new_mcOption = mcOptionTemplate(mcOptionData);
            // insert it into the page
            $('#question_'+questionJSON.question_id+' .enp-question__submit').before(new_mcOption);
        }

        // append the data to the mc options
        bindMCOptionData(questionJSON);

    } else if(questionJSON.question_type === 'slider') {

        // generate slider template
    }
}

/**
* Generate the Question Explanation off of JSON Data and the Underscore Template
* @param questionJSON
* @param correct (string) 'incorrect' or 'correct'
* @param callback (function) to run if you want to
* @return HTML of the explanation template with all data inserted
*/
function generateQuestionExplanation(questionJSON, correct, callback) {
    var question_response_percentage = questionJSON['question_responses_'+correct+'_percentage'];
    question_response_percentage = _.reformat_number(question_response_percentage, 100);
    explanationTemplate = questionExplanationTemplate({question_id: questionJSON.question_id, question_explanation: questionJSON.question_explanation, question_explanation_title: correct, question_explanation_percentage: question_response_percentage });
    if(typeof(callback) == "function") {
        callback(explanation);
    }
    return explanationTemplate;
}

/**
* Click on Next Question / Quiz End button
* 1. Prep the form values
* 2. Show the next question or quiz end template
* 3. Submit the form (so we can register a new page view/change the state of the quiz, etc)
*/
$(document).on('click', '.enp-next-step', function(e){
    e.preventDefault();
    url = $('.enp-question__form').attr('action');
    // add button value and name to the data since jQuery doesn't submit button value
    userAction = $(this).attr("name") + "=" + $(this).val();
    // add in a little data to let the server know the data is coming from an ajax call
    doing_ajax = 'doing_ajax=doing_ajax';
    data = $('.enp-question__form').serialize() + "&" + userAction + "&" + doing_ajax;

    // bring in the next question/quiz end
    $('.enp-question--on-deck').addClass('enp-question--show').removeClass('enp-question--on-deck');
    $('.enp-question__answered').addClass('enp-question--remove');
    $('.enp-question__container').removeClass('enp-question__container--explanation').addClass('enp-question__container--unanswered');

    // increase the question number and css
    questionShowJSON = $('.enp-question--show').data('questionJSON');
    questionNumber = questionShowJSON.question_order;
    questionNumber = parseInt(questionNumber) + 1;
    totalQuestions = $('.enp-quiz__progress__bar__question-count__total-questions').text();
    totalQuestions = parseInt(totalQuestions);
    progressBarWidth = (questionNumber/totalQuestions) * 100;
    console.log(questionNumber);
    if(progressBarWidth === 100) {
        progressBarWidth = 95;
    }
    progressBarWidth = progressBarWidth + '%';

    // BEM Taken WAAAAAAY too far...
    $('.enp-quiz__progress__bar__question-count__current-number').text(questionNumber);
    $('.enp-quiz__progress__bar').css('width', progressBarWidth);

    // submit the form
    $.ajax( {
        type: 'POST',
        url  : url,
        data : data,
        dataType : 'json',
    } )
    // success
    .done( questionExplanationSubmitSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {
        console.log( 'AJAX after finished' );
        $('.enp-question__answered').remove();
    } )
    .always(function() {

    });
});

/**
* On successful AJAX submit, either set-up the Next, Next question,
* or set-up the Quiz End state.
*
*/
function questionExplanationSubmitSuccess( response, textStatus, jqXHR ) {
    var responseJSON = $.parseJSON(jqXHR.responseText);
    console.log(responseJSON);
    if(responseJSON.state === 'quiz_end') {
        // remove the current explanation

        // see if there's a next question
        qEndTemplate = generateQuizEnd(responseJSON.quiz_end);
        $('.enp-question__form').append(qEndTemplate);
        $('.enp-results').addClass('.enp-question--on-deck').addClass('enp-question--show').removeClass('enp-question--on-deck');
        // make progress bar the full width
        $('.enp-quiz__progress__bar').css('width', '100%');

        // Append the text "Correct" to the number correct/incorrect
        $('.enp-quiz__progress__bar__question-count__total-questions').append(' Correct');
        // Change the first number to the amount they got correct
        $('.enp-quiz__progress__bar__question-count__current-number').text(responseJSON.quiz_end.score_total_correct);
    }



}

/**
* Find all the mc options in a container and tell us which is the correct one
* @param container (obj) wrapper for the inputs to search
* @param callback (function) Something to do with the correct one found
* @return input object that is correct
*/
function locateCorrectMCOption(container, callback) {
    var correct;
    $('.enp-option__input', container).each(function(e, obj) {
        if($(this).data('correct') === '1') {
            correct =  $(this);
            if(typeof(callback) == "function") {
                callback($(this));
            }
            return false;
        }
    });
    return correct;
}


/**
* Find all the mc options in a container and tell us which are incorrect
* @param container (obj) wrapper for the inputs to search
* @param callback (function) Something to do with the incorrect one found
* @return array of MC Input objects that are incorrect
*/
function locateIncorrectMCOptions(container, callback) {
    var incorrect;
    $('.enp-option__input', container).each(function(e, obj) {
        if($(this).data('correct') === '0') {
            incorrect =  $(this);
            if(typeof(callback) == "function") {
                callback($(this));
            }
        }
    });
    return incorrect;
}

/**
* Remove the MC Option from view by adding a class
* @param obj (jQuery object)
*/
function removeMCOption(obj) {
    if(!obj.hasClass('enp-option__input--incorrect-clicked')) {
        obj.addClass('enp-option__input--slide-hide');
    }
}

/**
* Highlight the correct MC Option by adding a class
* @param obj (jQuery object)
*/
function showCorrectMCOption(obj) {
    obj.addClass('enp-option__input--correct');
}

/**
* Attach data to the MC Options that lets us know if the
* mc option is correct or incorrect
* @param questionJSON (JSON String) Question level JSON
*/
function bindMCOptionData(questionJSON) {
    if(questionJSON.question_type === 'mc') {
        for(var prop in questionJSON.mc_option) {
            mc_option_id = questionJSON.mc_option[prop].mc_option_id;
            mc_option_correct = questionJSON.mc_option[prop].mc_option_correct;
            $('#enp-option__'+mc_option_id).data('correct', mc_option_correct);
        }
    }
}

/**
* Generate the Quiz End Template off of returned JSON Data and the Underscore Template
* @param quizEndJSON (JSON) data for the quiz end
* @param callback (function) to run if you want to
* @return HTML of the quiz end template with all data inserted
*/
function generateQuizEnd(quizEndJSON, callback) {
    quizEndData = {
                    score: quizEndJSON.score,
                    score_circle_dashoffset: quizEndJSON.score_circle_dashoffset,
                    quiz_end_title: quizEndJSON.quiz_end_title,
                    quiz_end_content: quizEndJSON.quiz_end_content,
    };
    qEndTemplate = quizEndTemplate(quizEndData);
    if(typeof(callback) == "function") {
        callback(explanation);
    }
    return qEndTemplate;
}

// on load, bind the initial question_json to the question id
bindQuestionData(init_question_json);
// on load, bind the initial question_json to the mc options, if it's an mc option question
bindMCOptionData(init_question_json);
});