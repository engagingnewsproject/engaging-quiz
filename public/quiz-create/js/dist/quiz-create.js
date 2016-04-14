jQuery( document ).ready( function( $ ) {// append ajax response message
function appendMessage(message, status) {
    var messageID = Math.floor((Math.random() * 1000) + 1);
    $('.enp-quiz-message-ajax-container').append('<div class="enp-quiz-message enp-quiz-message--ajax enp-quiz-message--'+status+' enp-container enp-message-'+messageID+'"><p class="enp-message__list enp-message__list--'+status+'">'+message+'</p></div>');

    $('.enp-message-'+messageID).delay(3500).fadeOut(function(){
        $('.enp-message-'+messageID).fadeOut();
    });
}

// Loop through messages and display them
// Show success messages
function displayMessages(message) {
    // loop through success messages
    //for(var success_i = 0; success_i < message.success.length; success_i++) {
        if(typeof message.success !== 'undefined' && message.success.length > 0) {
            appendMessage('Quiz Saved.', 'success');
        }

    //}

    // Show error messages
    for(var error_i = 0; error_i < message.error.length; error_i++) {
        appendMessage(message.error[error_i], 'error');
    }
}

/*
* What needs to happen on Load
*/

// ready the questions as accordions and add in swanky button template
$('.enp-question-content').each(function(i) {
    // set up accordions
    setUpAccordion($(this));
    // add in image upload button template if it doesn't have an image
    if($('.enp-question-image', this).length === 0) {
        $('.enp-question-image__input', this).after(questionImageUploadButtonTemplate());
    }
});

// hide descriptions
$('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input').hide();

// set-up our ajax response container for messages to get added to
$('#enp-quiz').append('<section class="enp-quiz-message-ajax-container"></section>');

function temp_removeMCOption(mcOptionID) {
    var mcOption;
    mcOption = $('#enp-mc-option--'+mcOptionID);
    // add keyboard focus to the next button element (either correct button or add option button)
    mcOption.next('.enp-mc-option').find('button:first').focus();
    // remove the mcOption
    mcOption.addClass('enp-mc-option--remove');
}

function removeMCOption(mcOptionID) {
    // actually remove it
    $('#enp-mc-option--'+mcOptionID).remove();
}

function temp_unsetRemoveMCOption(mcOptionID) {
    var mcOption;
    mcOption = $('#enp-mc-option--'+mcOptionID);
    // add keyboard focus to the next button element (either correct button or add option button)
    mcOption.removeClass('enp-mc-option--remove');
    // move focus back to mc option button delete
    $('.enp-mc-option__button--delete', mcOption).focus();
    // give them an error message
    appendMessage('Multiple Choice Option could not be deleted. Please reload the page and try again.', 'error');
}


// set MC Option as correct and unset all other mc options for that question
// this also UNSETs correct MCOption if it is already correct
function setCorrectMCOption(mcOptionID, questionID) {
    // check if it already has the enp-mc-option--correct class
    if($('#enp-mc-option--'+mcOptionID).hasClass('enp-mc-option--correct')) {
        // It's not NOT correct
        $('#enp-mc-option--'+mcOptionID).removeClass('enp-mc-option--correct');
        // nothing is right (correct)!
        return false;
    }
    // remove the correct class from the old one
    $('#enp-question--'+questionID+' .enp-mc-option').removeClass('enp-mc-option--correct');
    // add the correct class to the newly clicked one
    $('#enp-mc-option--'+mcOptionID).addClass('enp-mc-option--correct');
}

function temp_addMCOption(questionID) {
    // clone the template
    temp_mc_option = mcOptionTemplate({question_id: questionID, question_position: 'newQuestionPosition', mc_option_id: 'newMCOptionID', mc_option_position: 'newMCOptionPosition'});

    // insert it
    $('#enp-question--'+questionID+' .enp-mc-option--add').before(temp_mc_option);

    // focus it
    $('#enp-question--'+questionID+' .enp-mc-option--inputs:last .enp-mc-option__input').focus();
}

function unset_tempAddMCOption(questionID) {
    $('#enp-question--'+questionID+' #enp-mc-option--newMCOptionID').remove();
    appendMessage('Multiple Choice Option could not be added. Please reload the page and try again.', 'error');
}

// add MC option ID, question ID, question index, and mc option index
function addMCOption(new_mcOptionID, questionID) {

    new_mcOption_el = document.querySelector('#enp-question--'+questionID+' #enp-mc-option--newMCOptionID');

    // find/replace all index attributes (just in the name, but it'll search all attributes)
    findReplaceDomAttributes(new_mcOption_el, /newQuestionPosition/, getQuestionIndex(questionID));
    new_mc_option_index = $('#enp-question--'+questionID+' .enp-mc-option--inputs').length - 1;
    findReplaceDomAttributes(new_mcOption_el, /newMCOptionPosition/, new_mc_option_index);
    findReplaceDomAttributes(new_mcOption_el, /newMCOptionID/, new_mcOptionID);
}

function temp_addQuestionImage(question_id) {
    $('#enp-question--'+questionID+' .enp-question-image-upload').hide();
    $('#enp-question--'+questionID+' .enp-question-image-upload').after(waitSpinner('enp-image-upload-wait'));
}

function unset_tempAddQuestionImage(question_id) {
    $('#enp-question--'+questionID+' .enp-question-image-upload').show();
    $('#enp-question--'+questionID+' .enp-image-upload-wait').remove();

    appendMessage('Image could not be uploaded. Please reload the page and try again.', 'error');
}

function addQuestionImage(question) {
    questionID = question.question_id;
    $('#enp-question--'+questionID+' .enp-question-image-upload').remove();
    $('#enp-question--'+questionID+' .enp-question-image-upload__input').remove();
    $('#enp-question--'+questionID+' .enp-image-upload-wait').remove();


    // add the value for this question in the input field
    $('#enp-question--'+questionID+' .enp-question-image__input').val(question.question_image);

    // load the new image template
    templateParams ={question_id: questionID, question_position: getQuestionIndex(questionID)};
    $('#enp-question--'+questionID+' .enp-question-image__input').after(questionImageTemplate(templateParams));

    imageFile = question.question_image;
    // get the 580 wide one
    imageFile = imageFile.replace(/-original/g, '580w');

    // build the imageURL
    imageURL = quizCreate.quiz_image_url + $('#enp-quiz-id').val() + '/' + questionID + '/' + imageFile;

    // insert the image
    $('#enp-question--'+questionID+' .enp-question-image__container').prepend('<img class="enp-question-image enp-question-image" src="'+imageURL+'" alt="'+question.question_image_alt+'"/>');

}

function temp_removeQuestionImage(questionID) {
    $('#enp-question--'+questionID+' .enp-question-image__container').addClass('enp-question__image--remove');
    // set a temporary data attribute so we can get the value back if it doesn't save
    imageInput = $('#enp-question-image-'+questionID);
    imageFilename = imageInput.val();
    imageInput.data('image_filename', imageFilename);
    // unset the val in the image input
    imageInput.val('');

    console.log('old value is '+imageInput.data('image_filename'));

}

function temp_unsetRemoveQuestionImage(questionID) {
    $('#enp-question--'+questionID+' .enp-question-image__container').removeClass('enp-question__image--remove');

    // set the val in the image input back
    oldImageFilename = $('#enp-question-image-'+questionID).data('image_filename');
    $('#enp-question-image-'+questionID).val(oldImageFilename);
    // send an error message
    appendMessage('Image could not be deleted. Please reload the page and try again.', 'error');
}

function removeQuestionImage(question) {
    questionID = question.question_id;

    question = $('#enp-question--'+questionID);

    $('.enp-question-image__container', question).remove();
    // clear the input
    $('.enp-question-image__input', question).val('');

    // bring the labels back
    // load the new image template
    templateParams ={question_id: questionID, question_position: getQuestionIndex(questionID)};
    $('.enp-question-image__input',question).after(questionImageUploadTemplate(templateParams));
    // hide the upload button
    $('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input', question).hide();
    // bring the swanky upload image visual button back
    $('.enp-question-image__input',question).after(questionImageUploadButtonTemplate());
    // focus the button in case they want to upload a new one
    $('.enp-question-image-upload',question).focus();
}

$(document).on('click', '.enp-question-image-upload', function() {
    imageUploadInput = $(this).siblings('.enp-question-image-upload__input');
    imageUploadInput.trigger('click'); // bring up file selector
});

$(document).on('change', '.enp-question-image-upload__input',  function() {
    imageSubmit = $(this).siblings('.enp-button__question-image-upload');
    imageSubmit.trigger('click');
    // move focus to image description input
    imageSubmit.siblings('.enp-question-image-alt__input').focus();
});


function temp_addQuestion() {

    templateParams = {question_id: 'newQuestionTemplateID',
            question_position: 'newQuestionPosition'};

    // add the template in
    $('.enp-quiz-form__add-question').before(questionTemplate(templateParams));

    newQuestion = $('#enp-question--newQuestionTemplateID');
    // add the question upload area
    $('.enp-question-image-alt__label', newQuestion).before(questionImageUploadTemplate(templateParams));
    // hide the new image buttons
    $('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input', newQuestion).hide();

    // add the swanky image upload button
    // bring the swanky upload image visual button back
    $('.enp-question-image__input',newQuestion).after(questionImageUploadButtonTemplate());

    // add a temp MC option
    temp_addMCOption('newQuestionTemplateID');

    // set-up accordion
    // set-up question_content var
    setUpAccordion($('#enp-question--newQuestionTemplateID'));

    // focus the accordion button
    $('#enp-question--newQuestionTemplateID__accordion-header').focus();

}

// undo our temp action
function unset_tempAddQuestion() {
    // we didn't get a valid response from the server, so remove the question
    $('#enp-question--newQuestionTemplateID__accordion-header').remove();
    $('#enp-question--newQuestionTemplateID').remove();
    // give them an error message
    appendMessage('Question could not be added. Please reload the page and try again.', 'error');
}

// clone question, clear values, delete mc_options except one, add questionID, add MC option ID
function addQuestion(questionID, mcOptionID) {

    // find/replace all attributes and values on this question
    findReplaceDomAttributes(document.getElementById('enp-question--newQuestionTemplateID'), /newQuestionTemplateID/, questionID);
    // find replace on accordion
    findReplaceDomAttributes(document.getElementById('enp-question--newQuestionTemplateID__accordion-header'), /newQuestionTemplateID/, questionID);

    // find/replace all array index attributes
    findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), /newQuestionPosition/, getQuestionIndex(questionID));

    // change the default MCOptionIDs
    addMCOption(mcOptionID, questionID);
}


function temp_removeQuestion(questionID) {
    var accordionButton,
        question;
    // move the keyboard focus to the element BEFORE? the accordion
    // find the button
    accordionButton = $('#enp-question--'+questionID).prev('.enp-accordion-header');
    // remove the accordion button
    accordionButton.addClass('enp-question--remove');
    // find the question
    question = $('#enp-question--'+questionID);
    // move the keyboard focus to the element AFTER? the accordion
    question.next().focus();
    // remove the question
    question.addClass('enp-question--remove');
}

function temp_unsetRemoveQuestion(questionID) {
    var accordionButton,
        question;
    // move the keyboard focus to the element BEFORE? the accordion
    // find the button
    accordionButton = $('#enp-question--'+questionID).prev('.enp-accordion-header');
    // remove the accordion button
    accordionButton.removeClass('enp-question--remove');
    // find the question
    $('#enp-question--'+questionID).removeClass('enp-question--remove');

    appendMessage('Question could not be deleted. Please reload the page and try again.', 'error');
}

function removeQuestion(questionID) {
    // remove accordion
    $('#enp-question--'+questionID).prev('.enp-accordion-header').remove();
    // remove question
    $('#enp-question--'+questionID).remove();
}

// ajax submission
$(document).on('click', '.enp-quiz-submit', function(e) {

    if(!$(this).hasClass('enp-btn--next-step')) {
        e.preventDefault();
        // if new quiz flag is 1, then check for a title before continue
        if($('#enp-quiz-new').val() === '1') {
            // check for a title
            if($('.enp-quiz-title__textarea').val() === '') {
                $('.enp-quiz-title__textarea').focus();
                appendMessage('Please enter a title for your quiz.', 'error');
                return false;
            }
        }

        // add an "Are you sure about that?"
        if($(this).hasClass('enp-question__button--delete')) {
            // TODO This should be an "undo", not a confirm
            var confirmDelete = confirm('Are you sure you want to delete this question?');
            if(confirmDelete === false) {
                return false;
            }  else {
                // they want to delete it, so let them
                // TODO This should be an "undo", not a confirm
            }
        }

        // add a click wait, if necessary or r
        if($(this).hasClass('enp-quiz-submit--wait')) {
            console.log('waiting...');
            return false;
        } else {
            setWait();
        }

        // ajax send
        var userAction = $(this).val();
        // save the quiz
        saveQuiz(userAction);
    }
});

function saveQuiz(userAction) {
    var response,
        userActionAction,
        userActionElement;

    // get form
    var quizForm = document.getElementById("enp-quiz-create-form");
    // create formData object
    var fd = new FormData(quizForm);
    // set our submit button value
    fd.append('enp-quiz-submit', userAction);
    // append our action for wordpress AJAX call
    fd.append('action', 'save_quiz');

    // this sets up the immediate actions so it feels faster to the user
    // Optimistic Ajax
    setTemp(userAction);

    $.ajax( {
        type: 'POST',
         url  : quizCreate.ajax_url,
         data : fd,
         processData: false,  // tell jQuery not to process the data
         contentType: false,   // tell jQuery not to set contentType
    } )
    // success
    .done( quizSaveSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {
        console.log( 'AJAX after finished' );

    } )
    .always(function() {
        // remove wait class elements
        unsetWait();
    });
}

function quizSaveSuccess( response, textStatus, jqXHR ) {
    console.log(jqXHR.responseJSON);
    if(jqXHR.responseJSON === undefined) {
        // error :(
        unsetWait();
        appendMessage('Something went wrong. Please reload the page and try again.', 'error');
        return false;
    }

    response = $.parseJSON(jqXHR.responseJSON);

    userActionAction = response.user_action.action;
    userActionElement = response.user_action.element;
    // see if we've created a new quiz
    if(response.status === 'success' && response.action === 'insert') {
        // set-up quiz
        setNewQuiz(response);
    }
    // check user action
    if(userActionAction == 'add' && userActionElement == 'question') {
        var newQuestionResponse = getNewQuestion(response.question);

        if(newQuestionResponse !== false && newQuestionResponse.question_id !== undefined && parseInt(newQuestionResponse.question_id) > 0) {
            // we have a new question!
            new_questionID = newQuestionResponse.question_id;
            new_mcOption = getNewMCOption(new_questionID, response.question);
            addQuestion(new_questionID, new_mcOption.mc_option_id);
        } else {
            unset_tempAddQuestion();
        }
    }
    // remove Question
    else if(userActionAction == 'delete' && userActionElement == 'question') {
        // check to see if the action was completed
        questionID = response.user_action.details.question_id;
        questionResponse = checkQuestionSaveStatus(questionID, response.question);
        if(questionResponse !== false && questionResponse.action === 'update' && questionResponse.status === 'success') {
            removeQuestion(questionID);
        } else {
            temp_unsetRemoveQuestion(questionID);
        }

    } else if(userActionAction == 'delete' && userActionElement == 'mc_option') {
        // check to see if the action was completed
        var mcOptionID = response.user_action.details.mc_option_id;
        mcOptionResponse = checkMCOptionSaveStatus(mcOptionID, response.question);
        if(mcOptionResponse !== false && mcOptionResponse.action === 'update' && mcOptionResponse.status === 'success') {
            removeMCOption(mcOptionID);
        } else {
            temp_unsetRemoveMCOption(mcOptionID);
        }

    }
    // add mc_option
    else if(userActionAction == 'add' && userActionElement == 'mc_option') {
        // get the new inserted mc_option_id
        questionID = response.user_action.details.question_id;
        var newMCOptionResponse = getNewMCOption(questionID, response.question);
        if(newMCOptionResponse !== false && newMCOptionResponse.mc_option_id !== undefined && parseInt(newMCOptionResponse.mc_option_id) > 0) {
            // looks good! add the mc option
            addMCOption(newMCOptionResponse.mc_option_id, questionID);
        } else {
            // uh oh, something didn't go right. Remove it.
            unset_tempAddMCOption(questionID);
        }
    }
    // set correct mc_option
    else if(userActionAction == 'set_correct' && userActionElement == 'mc_option') {
        // set the correct one
        setCorrectMCOption(response.user_action.details.mc_option_id, response.user_action.details.question_id);
    }
    // add question image
    else if(userActionAction == 'upload' && userActionElement == 'question_image') {
        // check to see if the action was completed
        questionID = response.user_action.details.question_id;
        questionResponse = checkQuestionSaveStatus(questionID, response.question);
        if(questionResponse !== false && questionResponse.action === 'update' && questionResponse.status === 'success') {
            addQuestionImage(questionResponse);
        } else {
            temp_unsetAddQuestionImage(questionID);
        }
    }
    // remove image
    else if(userActionAction == 'delete' && userActionElement == 'question_image') {
        // check to see if the action was completed
        questionID = response.user_action.details.question_id;
        questionResponse = checkQuestionSaveStatus(questionID, response.question);
        if(questionResponse !== false && questionResponse.action === 'update' && questionResponse.status === 'success') {
            removeQuestionImage(questionResponse);
        } else {
            temp_unsetRemoveQuestionImage(questionID);
        }
    }
    // show ajax messages
    displayMessages(response.message);
}

function setNewQuiz(response) {
    $('#enp-quiz-id').val(response.quiz_id);

    // change the URL to our new one
    var html = $('body').innerHTML;
    var pageTitle = $('.enp-quiz-title__textarea').val();
    pageTitle = 'Quiz: '+pageTitle;
    var urlPath = quizCreate.quiz_create_url + response.quiz_id;
    window.history.pushState({"html":html,"pageTitle":pageTitle},"", urlPath);
}

function setTemp(userAction) {
    var pattern;
    // deleting a question
    console.log(userAction);
    if(userAction.indexOf('add-question') > -1) {
        // match the number for the ID
        temp_addQuestion();
    }
    else if(userAction.indexOf('add-mc-option__question') > -1) {
        pattern = /add-mc-option__question-/g;
        questionID = userAction.replace(pattern, '');
        temp_addMCOption(questionID);
    }
    else if(userAction.indexOf('question--delete') > -1) {
        // match the number for the ID
        pattern = /question--delete-/g;
        questionID = userAction.replace(pattern, '');
        temp_removeQuestion(questionID);
    }
    // deleting a mc option
    else if(userAction.indexOf('mc-option--delete') > -1) {
        // match the number for the ID
        pattern = /mc-option--delete-/g;
        mcOptionID = userAction.replace(pattern, '');
        temp_removeMCOption(mcOptionID);
    }
    // delete an image
    else if(userAction.indexOf('question-image--upload') > -1) {
        // match the number for the ID
        pattern = /question-image--upload-/g;
        questionID = userAction.replace(pattern, '');
        console.log(questionID);
        temp_addQuestionImage(questionID);
    }
    // delete an image
    else if(userAction.indexOf('question-image--delete') > -1) {
        // match the number for the ID
        pattern = /question-image--delete-/g;
        questionID = userAction.replace(pattern, '');
        console.log(questionID);
        temp_removeQuestionImage(questionID);
    }

}

// add wait classes to prevent duplicate submissions
// and add message/animation to show stuff is happening
function setWait() {
    // TODO: animation to show stuff is happening and they should wait a sec
    $('.enp-quiz-message-ajax-container').append('<div class="enp-quiz-message--saving">'+waitSpinner('enp-quiz-message--saving__spinner')+'<div class="enp-quiz-message--saving__text">Saving</div></div>');
    // add click wait class
    $('.enp-quiz-submit').addClass('enp-quiz-submit--wait');
}
// removes wait classes that prevent duplicate sumissions
function unsetWait() {
    $('.enp-quiz-submit').removeClass('enp-quiz-submit--wait');
    $('.enp-quiz-message--saving').remove();
}

/*
* Set-up Underscore Templates
*/
// set-up templates
// turn on mustache/handlebars style templating
_.templateSettings = {
  interpolate: /\{\{(.+?)\}\}/g
};
var questionTemplate = _.template($('#question_template').html());
var questionImageTemplate = _.template($('#question_image_template').html());
var questionImageUploadButtonTemplate = _.template($('#question_image_upload_button_template').html());
var questionImageUploadTemplate = _.template($('#question_image_upload_template').html());
var mcOptionTemplate = _.template($('#mc_option_template').html());
//$('#enp-quiz').prepend(questionTemplate({question_id: '999', question_position: '53'}));

/*
* Create utility functions for use across quiz-create.js
*/

function getQuestionIndex(questionID) {
    $('.enp-question-content').each(function(i) {
        if(parseInt($('.enp-question-id', this).val()) === parseInt(questionID)) {
            // we found it!
            questionIndex = i;
            // breaks out of the each loop
            return false;
        }
    });
    // return the found index
    return questionIndex;
}

// find the newly inserted mc_option_id
function getNewMCOption(questionID, question) {
    for (var prop in question) {
        // loop through the questions and get the one we want
        // then get the id of the newly inserted mc_option
        if(parseInt(question[prop].question_id) === parseInt(questionID)) {
            // now loop the mc options
            for(var mc_option_prop in question[prop].mc_option) {
                console.log(question[prop].mc_option[mc_option_prop]);
                if(question[prop].mc_option[mc_option_prop].action === 'insert') {
                    // here's our new mc option ID!
                    return question[prop].mc_option[mc_option_prop];
                }

            }
        }
    }
    return false;
}

function checkQuestionSaveStatus(questionID, question) {
    // loop through questions
    for (var prop in question) {
        // check if this question equals question_id that was trying to be deleted
        if(parseInt(question[prop].question_id) === parseInt(questionID)) {
            // found it! return the question JSON
            console.log(question[prop]);
            return question[prop];
        }
    }

    return false;
}

function checkMCOptionSaveStatus(mcOptionID, question) {
    // loop through questions
    for (var prop in question) {
        // check if this question equals question_id that was trying to be deleted
        for (var mc_option_prop in question[prop].mc_option) {
            if(parseInt(question[prop].mc_option[mc_option_prop].mc_option_id) === parseInt(mcOptionID)) {
                // found it! return the mc_option
                return question[prop].mc_option[mc_option_prop];
            }
        }
    }

    return false;
}

// Search for the question that was inserted in the json response
function getNewQuestion(question) {
    for (var prop in question) {
        if(question[prop].action === 'insert') {
            // this is our new question, because it was inserted and not updated
            return question[prop];
        }
    }
    return false;
}

// Add a loading animation
function waitSpinner(waitClass) {
    return '<div class="spinner '+waitClass+'"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
}

/** set-up accordions for questions
* @param obj: $('#jqueryObj') of the question you want to turn into an accordion
*/
function setUpAccordion(obj) {
    var accordion,
        question_title,
        question_content;
    // get the value for the title
    question_title = $('.enp-question-title__textarea', obj).val();
    // if it's empty, set it as an empty string
    if(question_title === undefined || question_title === '') {
        question_title = 'Question';
    }
    // set-up question_content var
    question_content = obj;
    // create the title and content accordion object so our headings can get created
    accordion = {title: question_title, content: question_content, baseID: obj.attr('id')};
    //returns an accordion object with the header object and content object
    accordion = enp_accordion__create_headers(accordion);
    // set-up all the accordion classes and start classes (so they're closed by default)
    enp_accordion__setup(accordion);
}

/**
* Replace all attributes with regex replace/string of an element
* and its children
*
* @param el: DOM element
* @param pattern: regex pattern for matching with replace();
* @param replace: string if pattern matches, what you want
*        the pattern to be replaced with
*/
function findReplaceDomAttributes(el, pattern, replace) {
    // replace on the passed dom attributes
    replaceAttributes(el, pattern, replace);
    // see if it has children
    if(el.children) {
        // loop the children
        // This function will also replace the attributes
        loopChildren(el.children, pattern, replace);
    }
}

/**
* Loop through the children of an element, replace it's attributes,
* and search for more children to loop
*
* @param nodes: el.children
* @param pattern: regex pattern for matching with replace();
* @param replace: string if pattern matches, what you want
*        the pattern to be replaced with
*/
function loopChildren(children, pattern, replace)
{
    var el;
    for(var i=0;i<children.length;i++)
    {
        el = children[i];
        // replace teh attributes on this element
        replaceAttributes(el, pattern, replace);

        if(el.children){
            loopChildren(el.children, pattern, replace);
        }

    }
}

/**
* replace all attributes on an element with regex replace()
* @param el: DOM element
* @param pattern: regex pattern for matching with replace();
* @param replace: string if pattern matches, what you want
*        the pattern to be replaced with
*/
function replaceAttributes(el, pattern, replace) {
    for (var att, i = 0, atts = el.attributes, n = atts.length; i < n; i++){
        att = atts[i];
        newAttrVal = att.nodeValue.replace(pattern, replace);

        // if the new val and the old val match, then nothing was replaced,
        // so we can skip it
        if(newAttrVal !== att.nodeValue) {

            if(att.nodeName === 'value') {
                // I heard value was trickier to track and update cross-browser,
                // so use jQuery til further notice...
                $(el).val(newAttrVal);
            } else {
                el.setAttribute(att.nodeName, newAttrVal);
            }
            console.log('Replaced '+att.nodeName+' '+att.nodeValue);
        }
    }
}

/*
* General UX interactions to make a better user experience
*/

// set titles as the values are being typed
$(document).on('keyup', '.enp-question-title__textarea', function() {
    // get the value of the textarea we're typing in
    question_title = $(this).val();
    // find the accordion header it goes with and add in the title
    $(this).closest('.enp-question-content').prev('.enp-accordion-header').text(question_title);
});
});