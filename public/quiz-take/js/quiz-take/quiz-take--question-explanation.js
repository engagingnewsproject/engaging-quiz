// load expanation template
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
    $(this).closest('.enp-question__fieldset').addClass('enp-question--remove');
    $('.enp-question__container').removeClass('enp-question__container--explanation').addClass('enp-question__container--unanswered');

    $.ajax( {
        type: 'POST',
        url  : url,
        data : data,
        dataType : 'json',
    } )
    // success
    .done( quizSaveNextStepSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {
        console.log( 'AJAX after finished' );
        $('.enp-question__fieldset:first').remove();
    } )
    .always(function() {

    });
});

function quizSaveNextStepSuccess( response, textStatus, jqXHR ) {
    var responseJSON = $.parseJSON(jqXHR.responseText);
    // see if there's a next question
    console.log(responseJSON);


}
