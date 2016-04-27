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
