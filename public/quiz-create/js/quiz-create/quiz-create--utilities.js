function getQuestions() {
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
// // // // // // // // // 
var currentSelector;
function addTinymce( obj ) {
    var currentSelector = $('#enp-question-explanation__'+obj+'');

    tinymce.init({
        selector: '#enp-question-explanation__'+obj+'',  // change this value according to your HTML
        menubar: false,
        statusbar: false,
        toolbar: false,
        inliine: true,
        plugins: 'quickbars link autosave',
        toolbar: 'bold italic link blockquote',
        quickbars_selection_toolbar: 'bold italic link blockquote',
        quickbars_insert_toolbar: false,
        quickbars_image_toolbar: false,
        link_assume_external_targets: 'http',
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
