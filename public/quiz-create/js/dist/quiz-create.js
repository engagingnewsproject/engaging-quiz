jQuery( document ).ready( function( $ ) {function getQuestions() {
    return $('.enp-question-content')
}

function getQuestion(questionID) {
    return $('#enp-question--'+questionID)
}

function getQuestionContainer(questionID) {
    return $('#enp-question--'+questionID+'__accordion-container')
}
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

// returns the question ID based on the question jQuery object in the DOM
function getQuestionID($question) {
    return parseInt($('.enp-question-id', $question).val())
}

function getQuestionByMCOptionID(mcOptionID) {
    return $('#enp-mc-option--'+mcOptionID).closest('.enp-question-content');
}

function getQuestionAccordionButton(questionID) {
    return getQuestion(questionID).prev('.enp-accordion-header');
}

// find the newly inserted mc_option_id
function getNewMCOption(questionID, question) {
    for (var prop in question) {
        // loop through the questions and get the one we want
        // then get the id of the newly inserted mc_option
        if(parseInt(question[prop].question_id) === parseInt(questionID)) {
            // now loop the mc options
            for(var mc_option_prop in question[prop].mc_option) {
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

function triggerSave() {
    $('.enp-quiz-form__save').trigger('click')
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
    question_title = processAccordionTitle(question_title)
    // set-up question_content var
    question_content = obj;
    // create the title and content accordion object so our headings can get created
    accordion = {
        title: question_title, 
        content: question_content, 
        baseID: obj.attr('id'), 
        container: true
    };
    //returns an accordion object with the header object and content object
    accordion = enp_accordion__create_headers(accordion);
    // wrap the accordion in a class

    // set-up all the accordion classes and start classes (so they're closed by default)
    enp_accordion__setup(accordion);
}

function processAccordionTitle(accordion_title) {
    // if it's empty, set it as an empty string
    if(accordion_title === undefined || accordion_title === '') {
        accordion_title = 'Question';
    }
    else if(accordion_title.length > 200) {
        // limit the length to 200 characters
        accordion_title = accordion_title.slice(0, 200)
        // add in an ellipse
        accordion_title += '…'
    }

    return accordion_title;
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
        }
    }
}

_.middleNumber = function(a, b) {
    return (a + b)/2;
};

// // // // // // // // // 
// Tinymce init for "add question" button
// CodePen of editor: https://codepen.io/luukee/pen/VwEmdBB
// // // // // // // // // 
var currentSelector;
function addTinymce( obj ) {
    var currentSelector = $('#enp-question-explanation__'+obj+'');

    tinymce.init({
        selector: '#enp-question-explanation__'+obj+'',
        menubar: false,
        statusbar: false,
        plugins: 'quickbars link autoresize',
        toolbar: false,
        quickbars_selection_toolbar: 'bold italic blockquote quicklink removeformat',
        link_default_target: "_blank",
        link_context_toolbar: true,
        link_title: false,
        link_assume_external_targets: "http",
        link_quicklink: true,
        quickbars_insert_toolbar: false,
        link_target_list: [
            { title: "New page", value: "_blank" },
        ],
        link_target_list: false,
        placeholder: 'Your cerebellum can predict your own actions, so you\'re unable to \'surprise\' yourself with a tickle.',
        setup: function (editor) {
            editor.on('click', function () {
                tinymce.activeEditor.execCommand('mceFocus');
            });
            editor.on('blur', function () {
                var tinyEditorContent = tinymce.activeEditor.getContent({format: 'raw'});
                var tContent = currentSelector.innerHTML = tinyEditorContent;
            });
        }
    });
}

// TODO: attempt to inject tinymce html
function setTinymceContent( element, editorContent ) {
    var html = editorContent;
    tinymce.activeEditor.setContent(html, {format: 'raw'});
    var data = $('#enp-quiz-create-form').serializeArray();
}

$('.enp-quiz-submit').click(function(e){
tinymce.triggerSave();
    $theQuestions = $('.enp-accordion-container');
    $.each($theQuestions, function(i) {
        obj = getQuestionID(this);
        $(this).find('#enp-question-explanation__'+obj+'');
        var editorContent = tinymce.activeEditor.getContent({format: 'raw'});
        var element = $(this);
        setTinymceContent( element, editorContent ) 
    });
});

function injectTinymce( obj ) {
$('.enp-question-content').each(function() {
    var accordion = $(this).find('.enp-answer-explanation__textarea').val();
    });
}

function addAnswerExplanationEditor( response ) {
    var $question,
        $question_id,

    // get the questions
    $question = response.question;

    // loop through all questions
    $( $question ).each(function( $question_id ) {
        // get the question_id of each
        $question_id = this.question_id;
        // click on any of the triggers go ahead and add the tinymce
            addTinymce( $question_id );
    });
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
var sliderTemplate = _.template($('#slider_template').html());
var sliderTakeTemplate = _.template($('#slider_take_template').html());
var sliderRangeHelpersTemplate = _.template($('#slider_take_range_helpers_template').html());
//$('#enp-quiz').prepend(questionTemplate({question_id: '999', question_position: '53'}));

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

// add our sliders into the templates
$('.enp-slider-options').each(function() {
    setUpSliderTemplate($(this));
});

// check if there are any questions. If there aren't, then don't show the save/preview buttons
var url = window.location.href;
var patt = new RegExp("quiz-create/new");
if(patt.test(url) === true) {
    hideSaveButton();
}

// check if there are any error messages
if($('.enp-message__item--error').length !== 0) {
    var re = /Question \d+/;
    // check each to see if we need to higlight a question
    $('.enp-message__item--error').each(function() {
        errorMessage = $(this).text();
        found = errorMessage.match(re);
        // if we found anything, process it
        if(found !== null) {
            // extract the number
            questionNumber = found[0].replace(/Question /, '');
            questionNumber = questionNumber - 1;
            questionHeader = $('.enp-question-content:eq('+questionNumber+')').prev('.enp-accordion-header');
            if(!questionHeader.hasClass('question-has-error')) {
                questionHeader.addClass('question-has-error');
            }
        }

    });

}

// tinymce: Prevent jQuery UI dialog from blocking focusin
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
    e.stopImmediatePropagation();
  }
});

// get each question container
$theQuestions = $('.enp-accordion-container');

// for each question. . .
$.each($theQuestions, function(i) {
    // get question id's
    obj = getQuestionID(this);
    // init tinymce for each question
    addTinymce( obj );
});

/*
* General UX interactions to make a better user experience
*/

// set titles as the values are being typed
$(document).on('keyup', '.enp-question-title__textarea', function() {
    // get the value of the textarea we're typing in
    question_title = processAccordionTitle($(this).val())
    
    // find the accordion header it goes with and add in the title
    $(this).closest('.enp-question-content').prev('.enp-accordion-header').find('.enp-accordion-header__title').text(question_title);
});


// a click on Preview or Publish nav just clicks the preview button instead
$(document).on('click', '.enp-quiz-breadcrumbs__link--preview, .enp-quiz-breadcrumbs__link--publish', function(e) {
    e.preventDefault();
    $('.enp-btn--next-step').trigger('click');
});


function hideSaveButton() {
    $('.enp-quiz-form__save, .enp-btn--next-step').hide();
}

function showSaveButton() {
    $('.enp-quiz-form__save').show().addClass('enp-quiz-form__save--reveal');
    $('.enp-btn--next-step').show().addClass('enp-btn--next-step--reveal');
    $('.enp-quiz-breadcrumbs__link--preview').removeClass('enp-quiz-breadcrumbs__link--disabled');
}
// set-up our ajax response container for messages to get added to
$('#enp-quiz').append('<section class="enp-quiz-message-ajax-container" aria-live="assertive"></section>');

// append ajax response message
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
            // append our new success message
            appendMessage(message.success[0], 'success');
        }

    //}

    // Show error messages
    for(var error_i = 0; error_i < message.error.length; error_i++) {
        appendMessage(message.error[error_i], 'error');
    }
}


function destroySuccessMessages() {
    $('.enp-quiz-message--success').remove();
}

function removeErrorMessages() {
    if($('.enp-quiz-message--error').length) {
        $('.enp-quiz-message--error').remove();
        $('.enp-accordion-header').removeClass('question-has-error');
    }

}

// set-up sortable
function setUpSortable() {
    // setup our move buttons
    $('.enp-question__move').remove()
    // move arrows to new position
    //$('.enp-accordion-container').prepend('<div class="enp-question__sort"><svg class="enp-icon enp-icon--sort enp-question__icon--sort-up"><use xlink:href="#icon-arrow-up"></use></svg><svg class="enp-icon enp-icon--sort enp-question__icon--sort-down"><use xlink:href="#icon-arrow-down"></use></svg></div>')

    // set-up sortable questions
    $( '.enp-quiz-create__questions' ).sortable({
            handle: '.enp-accordion-header',
            placeholder: 'enp-sort__placeholder',
            cancel: ''
    });
}
setUpSortable();

$( '.enp-quiz-create__questions' ).on( 'sortstart', function( event, ui ) {
    // add class to body
    $('body').addClass('ui-sortable--sorting');

    // refresh calculations because we might have hid some open ones
    $( this ).sortable( "refreshPositions" );
    // set the placeholder to be the height of the accordion button
    $(ui.placeholder).css('height', $(ui.item).height())
});

$( '.enp-quiz-create__questions' ).on( 'sortstop', function( event, ui ) {
 // remove class to body
    $('body').removeClass('ui-sortable--sorting');
})
$( '.enp-quiz-create__questions' ).on( 'sortupdate', function( event, ui ) {

    // check if the question moved is still in view
    setTimeout(function(){
        var elementTop = $(ui.item).offset().top;
        var elementBottom = elementTop + $(ui.item).outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
        if(elementBottom > viewportTop && elementTop < viewportBottom) {
            // console.log('in viewport')
        } else {
            // console.log('not in viewport')
            // scroll to the top 
            window.scrollTo(0, elementTop - 20)
        }
    }, 200);
        

    // we don't need to do any checks here. updateQuestionIndex handles it for us
    updateQuestionIndexes()
    // trigger a generic save
    triggerSave()
});


/**
 * Updates all question form arrays to match their current order in the DOM
 */
function updateQuestionIndexes() {
    $('.enp-question-content').each(function(i) {
        // update each question index/array order to match its spot in the DOM
        updateQuestionIndex(getQuestionID($(this)), i)
    });
}

/**
 * Updates question form array and order value to match its current place in the DOM
 */
function updateQuestionIndex(questionID, newQuestionIndex) {
   
    // find out if we need to update this index or not.
    var $question = $('#enp-question--'+questionID)
    var $questionOrder = $('.enp-question-order', $question)
    var currentIndex = $questionOrder.val();
    // if the index doesn't match the desired spot, update it
    if(parseInt(currentIndex) !== newQuestionIndex) {
        console.log('updating '+questionID+' from '+currentIndex+' to '+newQuestionIndex)
        // evaluates to /enp_question\[currentIndex\]/
        // not sure why you need the double \\ instead of just one like normal
        var pattern = new RegExp("enp_question\\["+currentIndex+"\\]")
        findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), pattern, 'enp_question['+newQuestionIndex+']')
        $questionOrder.val(newQuestionIndex)
    }
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

function temp_addQuestion() {

    templateParams = {question_id: 'newQuestionTemplateID',
            question_position: 'newQuestionPosition'};

    // add the template in
    $('.enp-quiz-create__questions').append(questionTemplate(templateParams));

    newQuestion = $('#enp-question--newQuestionTemplateID');
    questionImageUpload = questionImageUploadTemplate(templateParams);
    // add the question upload area
    $('.enp-question-image__input', newQuestion).after(questionImageUpload);
    // hide the new image buttons
    $('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input', newQuestion).hide();

    // add the swanky image upload button
    // bring the swanky upload image visual button back
    $('.enp-question-image__input',newQuestion).after(questionImageUploadButtonTemplate());

    // add a temp MC option
    temp_addMCOption('newQuestionTemplateID');

    // add a temp Slider
    temp_addSlider('newQuestionTemplateID');

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
function addQuestion(questionID, mcOptionID, sliderID) {

    // find replace on container
    findReplaceDomAttributes(document.getElementById('enp-question--newQuestionTemplateID__accordion-container'), /newQuestionTemplateID/, questionID);
    // find/replace all array index attributes
    findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), /newQuestionPosition/, getQuestionIndex(questionID));

    // change the default MCOptionIDs
    addMCOption(mcOptionID, questionID);

    // change the default sliderIds
    addSlider(sliderID, questionID);
}


function temp_removeQuestion(questionID) {
    var accordionContainer,
        question;
    // move the keyboard focus to the element BEFORE? the accordion
    // find the container
    accordionContainer = getQuestionContainer(questionID)
    // remove the accordion container
    accordionContainer.addClass('enp-question--remove');
    // find the question
    question = $('#enp-question--'+questionID);
    // move the keyboard focus to the element AFTER? the accordion
    accordionContainer.next().find('enp-question-content').focus();
    // remove the question
    // question.addClass('enp-question--remove');
}

function temp_unsetRemoveQuestion(questionID) {
    var accordionContainer,
        question;
    // move the keyboard focus to the element BEFORE? the accordion
    // find the button
    accordionContainer = getQuestionContainer(questionID)
    // remove the accordion container remove class
    accordionContainer.removeClass('enp-question--remove');
    // find the question
    // $('#enp-question--'+questionID).removeClass('enp-question--remove');

    appendMessage('Question could not be deleted. Please reload the page and try again.', 'error');
}

function removeQuestion(questionID) {
    // remove accordion
    getQuestionContainer(questionID).remove();
    // remove question
    // $('#enp-question--'+questionID).remove();
    // update the indexes
    updateQuestionIndexes()
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

function temp_removeMCOption(mcOptionID) {
    var mcOption;
    mcOption = $('#enp-mc-option--'+mcOptionID);
    // add keyboard focus to the next button element (either correct button or add option button)
    mcOption.next('.enp-mc-option').find('button:first').focus();
    // remove the mcOption
    mcOption.addClass('enp-mc-option--remove');
}

function removeMCOption(mcOptionID) {
    var $question
    // grab the question object
    $question = getQuestionByMCOptionID(mcOptionID)
    // actually remove the mc option
    $('#enp-mc-option--'+mcOptionID).remove();
    // reindex MC options for this question
    updateMCIndexes($question)
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

function temp_addSlider(questionID) {
    // clone the template
    temp_slider = sliderTemplate({
            question_id: questionID,
            question_position: 'newQuestionPosition',
            slider_id: 'newSliderID',
            slider_range_low: '0',
            slider_range_high: '10',
            slider_correct_low: '5',
            slider_correct_high: '5',
            slider_increment: '1',
            slider_prefix: '',
            slider_suffix: ''
        });

    // insert it
    $(temp_slider).appendTo('#enp-question--'+questionID+' .enp-question');

}

// add MC option ID, question ID, question index, and mc option index
function addSlider(new_sliderID, questionID) {

    new_slider_el = document.querySelector('#enp-question--'+questionID+' .enp-slider-options');

    // find/replace all index attributes (just in the name, but it'll search all attributes)
    findReplaceDomAttributes(new_slider_el, /newQuestionPosition/, getQuestionIndex(questionID));

    findReplaceDomAttributes(new_slider_el, /newSliderID/, new_sliderID);

    // set-up the UX (accordions, range buttons, etc) now that we have an ID
    setUpSliderTemplate('#enp-question--'+questionID+' .enp-slider-options');
}

function createSliderTemplate(container) {
    var sliderData;
    var slider_id = $('.enp-slider-id', container).val();
    // scrape the input values and create the template
    var sliderRangeLow = parseFloat($('.enp-slider-range-low__input', container).val());
    var sliderRangeHigh = parseFloat($('.enp-slider-range-high__input', container).val());
    var sliderIncrement = parseFloat($('.enp-slider-increment__input', container).val());
    var sliderStart = getSliderStart(sliderRangeLow, sliderRangeHigh, sliderIncrement);

    sliderData = {
        'slider_id': slider_id,
        'slider_range_low': sliderRangeLow,
        'slider_range_high': sliderRangeHigh,
        'slider_start': sliderStart,
        'slider_increment': sliderIncrement,
        'slider_prefix': $('.enp-slider-prefix__input', container).val(),
        'slider_suffix': $('.enp-slider-suffix__input', container).val(),
        'slider_input_size': $('.enp-slider-range-high__input', container).val().length
    };

    // create slider template
    slider = sliderTakeTemplate(sliderData);
    sliderExample = $('<div class="enp-slider-preview"></div>').html(slider);
    // insert it
    $(sliderExample).prependTo(container);
    // create a new label and insert it
    $('.enp-slider__label', container).after('<label for="enp-slider-input__'+slider_id+'" class="enp-label enp-label--slider-preview">Slider Preview</label>');
    // remove the old label
    $('.enp-slider__label', container).remove();
    // create the jQuery slider
    createSlider($('.enp-slider-input__input', container), sliderData);
}

// on change slider values
$(document).on('blur', '.enp-slider-range-low__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderRangeLow = parseFloat($(this).val());
    slider.slider('option', 'min',  sliderRangeLow);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('min', sliderRangeLow);

    // set midpoint
    setSliderStart(slider, sliderInput);
});

// on change slider values
$(document).on('input', '.enp-slider-range-low__input', function() {
    var low;
    // get input value
    low = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider_input__range-helper__number--low');
    sliderPreview.text(low);

});

// on change slider values
$(document).on('input', '.enp-slider-range-high__input', function() {
    var high;
    // get input value
    high = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider_input__range-helper__number--high');
    sliderPreview.text(high);
});

// update high range and max value
$(document).on('blur', '.enp-slider-range-high__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderRangeHigh = parseFloat($(this).val());
    slider.slider('option', 'max',  sliderRangeHigh);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('max', sliderRangeHigh);
    // set midpoint
    setSliderStart(slider, sliderInput);
});

/**
* If the high correct value range isn't being used, we need to keep
* the low and high correct values in sync.
*/
$(document).on('input', '.enp-slider-correct-low__input', function() {

    // Check if the high input is being used.
    // if it's NOT being used, we need to keep the high value in sync
    if($(this).data('correctRangeInUse') === false) {
        // get the high correct input from the data on the low input
        highCorrectInput = $(this).data('highCorrectInput');
        // get the low correct value
        lowCorrectVal = $(this).val();
        // make the high correct input match the low correct input
        highCorrectInput.val(lowCorrectVal);
    }
});

// update high range and max value
$(document).on('blur', '.enp-slider-increment__input', function() {
    sliderID = $(this).data('sliderID');
    slider = getSlider(sliderID);
    sliderIncrement = parseFloat($(this).val());
    slider.slider('option', 'step',  sliderIncrement);
    sliderInput = $("#enp-slider-input__"+sliderID);
    sliderInput.attr('step', sliderIncrement);

    // set midpoint
    setSliderStart(slider, sliderInput);
});

// update the slider prefix on value change
$(document).on('input', '.enp-slider-prefix__input', function() {
    var prefix;
    // get input value
    prefix = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider-input__prefix');
    sliderPreview.text(prefix);
});

// update the slider suffix on value change
$(document).on('input', '.enp-slider-suffix__input', function() {
    var suffix;
    // get input value
    suffix = $(this).val();
    sliderPreview = getSliderPreviewElement($(this), '.enp-slider-input__suffix');
    sliderPreview.text(suffix);
});

// set the slider to the middle point
function setSliderStart(slider, sliderInput) {
    low = slider.slider('option', 'min');
    high = slider.slider('option', 'max');
    interval = slider.slider('option', 'step');
    sliderValue = getSliderStart(low, high, interval);
    // set it
    slider.slider("value", sliderValue);
    sliderInput.val(sliderValue);
}

// calculate where the slider should start
function getSliderStart(low, high, interval) {
    low = parseFloat(low);
    high = parseFloat(high);
    interval = parseFloat(interval);

    totalIntervals = (high - low)/interval;
    middleInterval = ((totalIntervals/2)*interval) + low;
    remainder = middleInterval % interval;
    middleInterval = middleInterval - remainder;

    return middleInterval;
}

function getSliderPreviewElement(obj, element) {
    return obj.closest('.enp-slider-options').find(element);
}

$(document).on('click', '.enp-slider-correct-answer-range', function() {
    sliderID = $(this).data('sliderID');
    if($(this).hasClass('enp-slider-correct-answer-range--add-range')) {
        addSliderRange(sliderID);
    } else {
        removeSliderRange(sliderID);
    }
});


function removeSliderRange(sliderID) {
    var container,
        lowCorrectInput;
    container = getSliderOptionsContainer(sliderID);
    // get low input
    lowCorrectInput = $('.enp-slider-correct-low__input', container);

    setUpRemoveSliderRange(sliderID);
    // Set the button content and classes
    $('.enp-slider-correct-answer-range', container).removeClass('enp-slider-correct-answer-range--remove-range').addClass('enp-slider-correct-answer-range--add-range').html('<svg class="enp-icon enp-slider-correct-answer-range__icon"><use xlink:href="#icon-add"><title>Add</title></use></svg> Answer Range');

    // Make Correct High value equal the Low value
    lowCorrectVal = lowCorrectInput.val();
    $('.enp-slider-correct-high__input', container).val(lowCorrectVal);
    // set data attribute on the low input so we know if the high input needs to get updated or not
    lowCorrectInput.data('correctRangeInUse', false);
}

function setUpRemoveSliderRange(sliderID) {
    var container;
    container = getSliderOptionsContainer(sliderID);
    // hide the answer range high input and "to" thang
    $('.enp-slider-correct__helper', container).addClass('enp-slider-correct__helper--hidden').text('');
    // hide the input correct high container
    $('.enp-slider-correct-high__input-container', container).addClass('enp-slider-correct-high__input-container--hidden');
    // change the low correct label to Slider Answer (remove Low)
    $('.enp-slider-correct-low__label', container).text('Slider Answer');
}

function addSliderRange(sliderID) {
    var container,
        lowCorrectInput,
        highCorrectInput;

    container = getSliderOptionsContainer(sliderID);
    // get low input
    lowCorrectInput = $('.enp-slider-correct-low__input', container);
    // get high input
    highCorrectInput = $('.enp-slider-correct-high__input', container);

    $('.enp-slider-correct-low__label', container).text('Slider Answer Low');
    $('.enp-slider-correct__helper', container).removeClass('enp-slider-correct__helper--hidden').text('to');
    $('.enp-slider-correct-high__input-container', container).removeClass('enp-slider-correct-high__input-container--hidden');
    $('.enp-slider-correct-answer-range', container).removeClass('enp-slider-correct-answer-range--add-range').addClass('enp-slider-correct-answer-range--remove-range').html('<svg class="enp-icon enp-slider-correct-answer-range__icon"><use xlink:href="#icon-close"><title>Remove Answer Range</title></use></svg>');
    // Add one interval to the high value if it equals the low value
    highCorrectVal = parseFloat( highCorrectInput.val() );
    lowCorrectVal = parseFloat( lowCorrectInput.val() );
    increment = parseFloat( $('.enp-slider-increment__input', container).val() );
    if(highCorrectVal <= lowCorrectVal) {
        highCorrectInput.val(lowCorrectVal + increment);
    }
    // focus the slider range high input
    highCorrectInput.focus();
    // set data attribute on the low input so we know if the high input needs to get updated or not
    lowCorrectInput.data('correctRangeInUse', true);
}

/**
* Get the fieldset that defines all the slider options
* @return jQuery element that is the fieldset containing the sliderID
*/
function getSliderOptionsContainer(sliderID) {
    var sliderOptionsContainer;
    $('.enp-slider-options').each(function() {
        // check its sliderID
        if($(this).data('sliderID') === sliderID) {
            // if it equals, then set the slider var and break out of the each loop
            sliderOptionsContainer = $(this);
            // breaks out of loop now that we have the value
            return false;
        }
    });
    return sliderOptionsContainer;
}


function setUpSliderTemplate(sliderOptionsContainer) {

    sliderID = $('.enp-slider-id', sliderOptionsContainer).val();
    // add data to slider options container
    $(sliderOptionsContainer).data('sliderID', sliderID);
    createSliderTemplate(sliderOptionsContainer);


    // get low correct input
    lowCorrectInput = $('.enp-slider-correct-low__input', sliderOptionsContainer);
    // get high correct input
    highCorrectInput = $('.enp-slider-correct-high__input', sliderOptionsContainer);
    // See if we should hide the slider answer high and add in the option to add in a high value
    // link the high and low inputs together so we can easily find them as needed
    lowCorrectInput.data('highCorrectInput', highCorrectInput);
    highCorrectInput.data('lowCorrectInput', lowCorrectInput);
    correctLow = parseFloat( lowCorrectInput.val() );
    correctHigh = parseFloat( highCorrectInput.val() );

    // see if the inputs are readonly or not.
    // if they're readonly, then they're published, so don't edit them
    if(lowCorrectInput.is('[readonly]')) {
        if( correctLow === correctHigh ) {
            // this will edit the text and remove the answer-range-high field
            setUpRemoveSliderRange(sliderID);
        }
    } else {
        // we're not published, so add all the functionality

        // add in the correct answer range selector button
        $('.enp-slider-correct-high__container', sliderOptionsContainer).append('<button class="enp-slider-correct-answer-range" type="button"></button>');

        // check if the answers match or not
        if( correctLow === correctHigh ) {
            removeSliderRange(sliderID);
        } else {
            addSliderRange(sliderID);
        }

    }

    // add the sliderID to all the inputs
    $('input, button', sliderOptionsContainer).each(function() {
        $(this).data('sliderID', sliderID);
    });


    // set-up accordion for advanced options
    // create the title and content accordion object so our headings can get created
    accordion = {title: 'Advanced Slider Options', content: $('.enp-slider-advanced-options__content', sliderOptionsContainer), baseID: sliderID};
    //returns an accordion object with the header object and content object
    accordion = enp_accordion__create_headers(accordion);
    // set-up all the accordion classes and start classes (so they're closed by default)
    enp_accordion__setup(accordion);
}


// ajax submission
$(document).on('click', '.enp-quiz-submit', function(e) {

    // tinymce.triggerSave();

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
    tinyMCE.triggerSave();
    // desroy successs messages so they don't stack
    destroySuccessMessages();

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

    } )
    .always(function() {
        // remove wait class elements
        unsetWait();
    });
}

function quizSaveSuccess( response, textStatus, jqXHR ) {
    // console.log(jqXHR.responseJSON);
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
        // show the preview/save buttons
        showSaveButton();
    }
    // check user action
    if(userActionAction == 'add' && userActionElement == 'question') {
        var newQuestionResponse = getNewQuestion(response.question);

        if(newQuestionResponse !== false && newQuestionResponse.question_id !== undefined && parseInt(newQuestionResponse.question_id) > 0) {
            // we have a new question!
            new_questionID = newQuestionResponse.question_id;
            new_mcOption = getNewMCOption(new_questionID, response.question);
            new_sliderID = newQuestionResponse.slider.slider_id;
            addQuestion(new_questionID, new_mcOption.mc_option_id, new_sliderID);
            addAnswerExplanationEditor( response );
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
    // remove error messages. Let the preview button handle that.
    // It's confusing if you click Save after making changes and error messages
    // don't go away. So, rather than check everything right now
    // (we should later) let's just remove all error messages til the next check
    removeErrorMessages();
}

function setNewQuiz(response) {
    $('#enp-quiz-id').val(response.quiz_id);

    // change the URL to our new one
    var html = $('body').innerHTML;
    var pageTitle = $('.enp-quiz-title__textarea').val();
    pageTitle = 'Quiz: '+pageTitle;
    var urlPath = quizCreate.quiz_create_url + response.quiz_id;
    addAnswerExplanationEditor( response );
    window.history.pushState({"html":html,"pageTitle":pageTitle},"", urlPath);
}

function setTemp(userAction) {
    var pattern;
    // deleting a question
    if(userAction.indexOf('add-question') > -1) {
        // match the number for the ID
        temp_addQuestion();
        // addAnswerExplanationEditor( response );
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
        temp_addQuestionImage(questionID);
    }
    // delete an image
    else if(userAction.indexOf('question-image--delete') > -1) {
        // match the number for the ID
        pattern = /question-image--delete-/g;
        questionID = userAction.replace(pattern, '');
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

function bindSliderData(questionJSON) {
    // assigns data and creates the jQuery slider
    question = $('#question_'+questionJSON.question_id);
    sliderInput = $('.enp-slider-input__input', question);
    // bind slider JSON data
    sliderInput.data('sliderJSON', questionJSON.slider);
    // create the jQuery slider
    createSlider(sliderInput, questionJSON.slider);
}

/**
* Creates a jQuery slider and injects it after the parent wrapper of the enp slider input
* @param sliderInput $('.enp-slider-input')
* @param sliderData {'sliderID': ID,
*                   'sliderStart': int,
*                   'sliderRangeLow': int,
*                   'sliderRangeHigh': int,
*                   'sliderIncrement': int
*                   }
*/
function createSlider(sliderInput, sliderData) {
    // create the div
    slider = $('<div class="enp-slider" aria-hidden="true" role="presentation"></div>');
    // add data
    slider.data('sliderID', sliderData.slider_id);
    $(sliderInput).data('sliderID', sliderData.slider_id);
    // create the jquery slider
    $(slider).slider({
        range: "min",
        value: parseFloat(sliderData.slider_start),
        min: parseFloat(sliderData.slider_range_low),
        max: parseFloat(sliderData.slider_range_high),
        step: parseFloat(sliderData.slider_increment),
        slide: function( event, ui ) {
            $( sliderInput ).val( ui.value );
        }
    });
    // get the slider input container
    sliderInputContainer = $(sliderInput).parent();
    // inject the slider to the DOM after the parent wrapper of the input
    sliderInputContainer.after(slider);

    // get the slider range helper template
    sliderTakeRangeHelpers = sliderRangeHelpersTemplate({
            'slider_range_low': parseFloat(sliderData.slider_range_low),
            'slider_range_high': parseFloat(sliderData.slider_range_high)
    });
    // add in the slider range helpers
    sliderInputContainer.append(sliderTakeRangeHelpers);
}

// update slider on value change
$(document).on('input', '.enp-slider-input__input', function(){
    var slider,
        sliderID,
        inputVal;

    // get the ID
    sliderID = $(this).data('sliderID');
    // get the Slider
    slider = getSlider(sliderID);
    // get the input value
    inputVal = $(this).val();
    // update the slider value
    slider.slider("value", inputVal);
    // check to see if the slider value matches the input value
    // the slider will not allow a min/max value outside the allowed slider min/max
    sliderVal = slider.slider("value");
});

// when leaving focus of the input, check to see if it's a valid entry
// and change it if it isn't
$(document).on('blur', '.enp-slider-input__input', function(){
    var slider,
        sliderID,
        inputVal,
        sliderVal;

    // input
    input = $(this);
    // get the ID
    sliderID = $(this).data('sliderID');
    // get the Slider
    slider = getSlider(sliderID);
    // get the input value
    inputVal = input.val();
    // get the slider value
    sliderVal = slider.slider("value");
    // compare the slider and input values
    if(parseInt(sliderVal) !== parseInt(inputVal)) {
        // if they don't match, then then input value is invalid
        // because the jQuery slider won't set the slider value to be outside the
        // accepted min/max range
        // Set the input to the slider value
        input.val(sliderVal);
        // flash red animation to show that we changed it
        input.addClass('enp-slider-input__input--invalid-animation');
        // remove the invalid class after half of a second
        setTimeout(
            function() {
                input.removeClass('enp-slider-input__input--invalid-animation');
            },
            500
        );

    }
});


function getSlider(sliderID, callback) {
    var slider;
    // find the slider with the slider ID that matches
    $('.enp-slider').each(function() {
        // check it's sliderID
        if($(this).data('sliderID') === sliderID) {
            // if it equals, then set the slider var and break out of the each loop
            slider = $(this);
            if(typeof(callback) == "function") {
                callback($(this));
            }
            return false;
        }
    });
    return slider;
}

function buildSlider(questionJSON) {
    sliderJSON = questionJSON.slider;
    sliderData = {
                    slider_id: sliderJSON.slider_id,
                    slider_range_low: sliderJSON.slider_range_low,
                    slider_range_high: sliderJSON.slider_range_high,
                    slider_correct_low: sliderJSON.slider_correct_low,
                    slider_correct_high: sliderJSON.slider_correct_high,
                    slider_increment: sliderJSON.slider_increment,
                    slider_start: sliderJSON.slider_start,
                    slider_prefix: sliderJSON.slider_prefix,
                    slider_suffix: sliderJSON.slider_suffix,
                    slider_input_size: sliderJSON.slider_range_high.length
                };
    // generate slider template
    slider = sliderTemplate(sliderData);
    // inject the slider template into the page
    $('#question_'+questionJSON.question_id+' .enp-question__submit').before(slider);
    // bind the data to the slider
    bindSliderData(questionJSON);
}

function processSliderSubmit(questionFieldset) {

    sliderInput = $('.enp-slider-input__input', questionFieldset);
    slider = $('.enp-slider', questionFieldset);

    // disable the slider and input
    sliderInput.attr('disabled', 'disabled');
    slider.slider('disable');

    // get the value they entered in the slider input
    sliderSubmittedVal = parseFloat(sliderInput.val());
    // get sliderJSON
    sliderJSON = sliderInput.data('sliderJSON');
    sliderCorrectLow = parseFloat(sliderJSON.slider_correct_low);
    sliderCorrectHigh = parseFloat(sliderJSON.slider_correct_high);

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
        correctLowIntervals = (sliderCorrectLow-sliderRangeLow)/sliderIncrement;

        correctHighIntervals = (sliderCorrectHigh-sliderRangeLow)/sliderIncrement;
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

    return correct_string;
}
});