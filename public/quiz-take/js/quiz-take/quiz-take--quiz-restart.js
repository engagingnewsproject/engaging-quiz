/**
* Click listener for restarting a quiz.
*/
$(document).on('click', '.enp-quiz-restart', function(e){
    e.preventDefault();
    // send a message to the parent frame that a restart was initiated
    sendPostMessageAction('quizRestarted');

    //$(this).submit();
});
