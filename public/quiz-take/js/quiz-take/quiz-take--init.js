// on load, bind the initial question_json to the question id
bindQuestionData(init_question_json);
// on load, bind the initial question_json to the mc options, if it's an mc option question
bindMCOptionData(init_question_json);
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
