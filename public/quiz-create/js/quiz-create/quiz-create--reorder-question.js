/*
* Create utility functions for use across quiz-create.js
*/

function updateQuestionIndex(questionID, newQuestionIndex) {
   
    // find out if we need to update this index or not.
    var $question = $('#enp-question--'+questionID)
    var $questionOrder = $('.enp-question-order', $question)
    var currentIndex = $questionOrder.val();
    if(parseInt(currentIndex) !== newQuestionIndex) {
        console.log('replacing '+currentIndex+ ' to '+newQuestionIndex)
        // evaluates to /enp_question\[currentIndex\]/
        // not sure why you need the double \\ instead of just one like normal
        var pattern = new RegExp("enp_question\\["+currentIndex+"\\]")
        console.log(pattern)
        findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), pattern, 'enp_question['+newQuestionIndex+']')
        $questionOrder.val(newQuestionIndex)
    }
}


function updateQuestionIndexes() {
    $('.enp-question-content').each(function(i) {
        console.log('updating '+getQuestionID($(this))+' index to '+i)
        updateQuestionIndex(getQuestionID($(this)), i)
    });
}


function updateMCIndex($mcOption, newMCOptionIndex) {
   
    // find out if we need to update this index or not.
    var $mcOrder = $('.enp-mc-option-order', $mcOption)
    var currentIndex = $mcOrder.val();
    if(currentIndex === undefined) {
        console.log($mcOrder)
    }
    if(parseInt(currentIndex) !== newMCOptionIndex) {
        console.log('replacing '+currentIndex+ ' to '+newMCOptionIndex)

        var pattern = new RegExp("\\]\\[mc_option\\]\\["+currentIndex+"\\]\\[")
        console.log(pattern)
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
