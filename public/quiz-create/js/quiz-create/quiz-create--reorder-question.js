/*
* Create utility functions for use across quiz-create.js
*/

function updateQuestionIndex(questionID, newQuestionIndex) {
   
    // find out if we need to update this index or not.
    var $question = $('#enp-question--'+questionID)
    var $questionOrder = $('.enp-question-order', $question)
    var currentIndex = $questionOrder.val();
    if(parseInt(currentIndex) !== newQuestionIndex) {
        // evaluates to /enp_question\[currentIndex\]/
        // not sure why you need the double \\ instead of just one like normal
        var pattern = new RegExp("enp_question\\["+currentIndex+"\\]")
        findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), pattern, 'enp_question['+newQuestionIndex+']')
        $questionOrder.val(newQuestionIndex)
    }
}


function updateQuestionIndexes() {
    $('.enp-question-content').each(function(i) {
        updateQuestionIndex(getQuestionID($(this)), i)
    });
}

/**
 * Move a question from one index to another in the UI 
 * (as well as update indexes in the form)
 *
 * @param questionID INT question ID you want to move
 * @param to INT index you want to move the question to
 * @return BOOLEAN
 */ 
function moveQuestion(questionID, to) {
    var $question, 
        questionIndex, 
        $questionButton, 
        $anchorQuestion, 
        $anchorQuestionButton, 
        $prevQuestion,
        $questions
    // get the question
    var $question = getQuestion(questionID)

    // get the current index of the question
    var questionIndex = getQuestionIndex(questionID)
    // bail if it's already there
    if(questionIndex === to) {
        return false
    }

    // get the accordion button attached to the question
    var $questionButton = getQuestionAccordionButton(questionID)
    
    $questions = getQuestions()
    // check if we're moving it to be the last question
    // move just the button for now. we'll insert the question after the button later on
    if(to === ($questions.length - 1)) {
        $anchorQuestion = $questions[$questions.length - 1]
        $questionButton.insertAfter($anchorQuestion)
    } else {
        $anchorQuestionButton = getQuestionAccordionButton( getQuestionID($questions[to]) )
        $questionButton.insertBefore($anchorQuestionButton)
    }

    // insert the question after its already-moved accordion button
    $question.insertAfter($questionButton)

    // update indexes in the form
    updateQuestionIndexes()

    return true
}


function updateMCIndex($mcOption, newMCOptionIndex) {
   
    // find out if we need to update this index or not.
    var $mcOrder = $('.enp-mc-option-order', $mcOption)
    var currentIndex = $mcOrder.val();

    if(parseInt(currentIndex) !== newMCOptionIndex) {
        var pattern = new RegExp("\\]\\[mc_option\\]\\["+currentIndex+"\\]\\[")
        findReplaceDomAttributes(document.getElementById($mcOption.attr('id')), pattern, '][mc_option]['+newMCOptionIndex+'][')
        $mcOrder.val(newMCOptionIndex)
    }
}


function updateMCIndexes($question) {
    // get all options and loop throught them
    $('.enp-mc-option--inputs', $question).each(function(i) {
        updateMCIndex($(this), i)
    });
}
