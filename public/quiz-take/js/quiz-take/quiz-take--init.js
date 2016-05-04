// on load, bind the initial question_json to the question id
// check if the init_question_json variable exists
if(typeof init_question_json !== 'undefined') {
    bindQuestionData(init_question_json);
    // on load, bind the initial question_json to the mc options, if it's an mc option question
    bindMCOptionData(init_question_json);
}

// on load, bind the quiz data to the quiz DOM
bindQuizData(quiz_json);

/**
* Binds JSON data to the quiz form element in the DOM so we always have
* access to it. Accessible via
* $('#quiz').data('quizJSON');
*/
function bindQuizData(quizJSON) {
    $('#quiz').data('quizJSON', quizJSON);
}

function getQuizID() {
    json = $('#quiz').data('quizJSON');
    return json.quiz_id;
}

// send body height on init
sendBodyHeight();

// after images are loaded, send the height again
/*$('.enp-question-image').load(function() {
    console.log('image loaded');
    sendBodyHeight();
});*/
