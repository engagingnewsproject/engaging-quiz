jQuery( document ).ready( function( $ ) {

    // ready the questions as accordions
    $('.enp-question-content').each(function(i) {
        var accordion,
            question_title,
            question_content;

        // get the value for the title
        question_title = $('.enp-question-title__textarea', this).val();
        // if it's empty, set it as an empty string
        if(question_title === undefined || question_title === '') {
            question_title = 'Question';
        }
        // set-up question_content var
        question_content = $(this);
        // create the title and content accordion object so our headings can get created
        accordion = {title: question_title, content: question_content, baseID: $(this).attr('id')};
        //returns an accordion object with the header object and content object
        accordion = enp_accordion__create_headers(accordion);
        // set-up all the accordion classes and start classes (so they're closed by default)
        enp_accordion__setup(accordion);
    });

    // hide descriptions
    $('.enp-button__question-image-upload, .enp-question-image-upload__input').hide();

    //*** END SETUP ***//

    // set titles as the values are being typed
    $(document).on('keyup', '.enp-question-title__textarea', function() {
        // get the value of the textarea we're typing in
        question_title = $(this).val();
        // find the accordion header it goes with and add in the title
        $(this).closest('.enp-question-content').prev('.enp-accordion-header').text(question_title);

    });

    // ajax submission
    $(document).on('click', '.enp-quiz-submit', function(e) {
        if(!$(this).hasClass('enp-btn--next-step')) {
            e.preventDefault();

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
                // TODO: animation to show stuff is happening and they should wait a sec
            } else {
                setWait();
            }

            // ajax send
            var userAction = $(this).val();
            // this sets up the immediate actions so it's faster to respond
            setTemp(userAction);
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

        $.ajax( {
            type: 'POST',
             url  : quizCreate.ajax_url,
             data : fd,
             processData: false,  // tell jQuery not to process the data
             contentType: false,   // tell jQuery not to set contentType
             beforeSend : function( d ) {
                console.log( 'Before send', d );
             }
        } )
        // success
        .done( function( response, textStatus, jqXHR ) {
            // console.log( 'AJAX done', textStatus, jqXHR, jqXHR.getAllResponseHeaders() );
            //console.log( 'AJAX done', jqXHR.responseJSON );
            console.log(jqXHR.responseJSON);
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
        } )
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

    // set-up our ajax response container
    $('#enp-quiz').append('<section class="enp-quiz-message-ajax-container"></section>');
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

    // append ajax response messages
    function appendMessage(message, status) {
        var messageID = Math.floor((Math.random() * 1000) + 1);
        $('.enp-quiz-message-ajax-container').append('<div class="enp-quiz-message enp-quiz-message--ajax enp-quiz-message--'+status+' enp-container enp-message-'+messageID+'"><p class="enp-message__list enp-message__list--'+status+'">'+message+'</p></div>');

        $('.enp-message-'+messageID).delay(3500).fadeOut(function(){
            $('.enp-message-'+messageID).fadeOut();
        });
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


    function temp_addQuestion() {
        var templateQuestion,
            templateAccordionHeader,
            newQuestion,
            newAccordionHeader;

        templateAccordionHeader = $('#enp-question--questionTemplateID__accordion-header');
        templateQuestion = $('#enp-question--questionTemplateID');
        newAccordionHeader = templateAccordionHeader.clone();
        newQuestion = templateQuestion.clone();

        // just temporarily so the CSS won't hide it and we have a hook to find it
        // change the accordion header ID
        findReplaceAttr(newAccordionHeader, 'id', /questionTemplateID/, 'newQuestionTemplateID');
        // change the question id
        findReplaceAttr(newQuestion, 'id', /questionTemplateID/, 'newQuestionTemplateID');

        // remove the image part (they can't already have an image for it...)
        $('.enp-question-image__container', newQuestion).remove();

        newQuestion.insertBefore(templateAccordionHeader);
        newAccordionHeader.insertBefore(newQuestion);
    }

    // undo our temp action
    function unset_tempAddQuestion() {
        // we didn't get a valid response from the server, so remove the question
        $('#enp-question--newQuestionTemplateID__accordion-header').remove();
        $('#enp-question--newQuestionTemplateID').remove();
        // give them an error message
        appendMessage('Question could not be added. Please reload the page and try again.', 'error');
    }

    function getNewQuestion(question) {
        for (var prop in question) {
            if(question[prop].action === 'insert') {
                // this is our new question, because it was inserted and not updated
                return question[prop];
            }
        }
        return false;
    }

    // clone question, clear values, delete mc_options except one, add questionID, add MC option ID
    function addQuestion(questionID, mcOptionID) {
        var question,
            accordionHeader;
        // new accordion header
        accordionHeader = $('#enp-question--newQuestionTemplateID__accordion-header');
        // new question
        question = $('#enp-question--newQuestionTemplateID');

        // find/replace all attributes and values on this question
        findReplaceDomAttributes(document.getElementById('enp-question--newQuestionTemplateID'), /questionTemplateID/, questionID);

        // change the accordion header ID
        findReplaceAttr(accordionHeader, 'id', /newQuestionTemplateID/, questionID);
        // change the question classes/ids
        findReplaceAttr(question, 'id', /newQuestionTemplateID/, questionID);

        // change the default MCOptionIDs
        addMCOption(mcOptionID, questionID);

        // change the question array iterator
        // setup question index and regex
        question_index = getQuestionIndex(questionID);
        question_index_pattern = /enp_question\[questionCounterTemplate\]/;
        question_index_replace = 'enp_question['+question_index+']';
        // find/replace all index attributes (just in the name, but it'll search all attributes)
        // TODO: limit search by inputs/textareas?
        // TODO: Integrate this into the previous search so it's just one loop instead of several?
        findReplaceDomAttributes(document.getElementById('enp-question--'+questionID), question_index_pattern, question_index_replace);
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
        var new_mcOption = $('#enp-question--questionTemplateID .enp-mc-option:first').clone();
        // insert it
        new_mcOption.insertBefore($('#enp-question--'+questionID+' .enp-mc-option--add'));
        // focus it
        $('#enp-question--'+questionID+' .enp-mc-option--inputs:last .enp-mc-option__input').focus();
    }

    function unset_tempAddMCOption(questionID) {
        $('#enp-question--'+questionID+' #enp-mc-option--mcOptionTemplateID').remove();
        appendMessage('Multiple Choice Option could not be added. Please reload the page and try again.', 'error');
    }

    // add MC option ID, question ID, question index, and mc option index
    function addMCOption(new_mcOptionID, questionID) {

        var new_mcOption,
            addMCOptionButton;
        new_mcOption = $('#enp-question--'+questionID+' #enp-mc-option--mcOptionTemplateID');

        new_mcOption_el = document.querySelector('#enp-question--'+questionID+' #enp-mc-option--mcOptionTemplateID');

        // change the id
        $('.enp-mc-option-id', new_mcOption).val(new_mcOptionID);
        // change the delete button value
        $('.enp-mc-option__button--delete', new_mcOption).val('mc-option--delete-'+new_mcOptionID);
        // change the correct button value
        $('.enp-mc-option__button--correct', new_mcOption).val('mc-option--correct__question-'+questionID+'__mc-option-'+new_mcOptionID);
        // change the ID
        $(new_mcOption).attr('id', 'enp-mc-option--'+new_mcOptionID);

        // setup question index and regex
        question_index = getQuestionIndex(questionID);
        question_index_pattern = /enp_question\[(.*?)\]/;
        // setup mcoption index and regex
        mc_option_pattern = /\[mc_option\]\[(.*?)\]/;
        new_mc_option_index = $('#enp-question--'+questionID+' .enp-mc-option__input').length - 1;
        // reindex/change the input names
        $('input', new_mcOption).each(function () {
            var inputName = $(this).prop('name');
            // change the index of the form array for the question
            new_inputName = inputName.replace(question_index_pattern, 'enp_question['+question_index+']');
            $(this).attr('name', new_inputName);

            // get it again, because it's been updated
            inputName = $(this).prop('name');
            // change the index of the form array for the mc_option
            new_inputName = inputName.replace(mc_option_pattern, '[mc_option]['+new_mc_option_index+']');
            console.log(new_inputName);
            $(this).attr('name', new_inputName);
        });
    }

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

    function temp_addQuestionImage(question_id) {
        $('#enp-question--'+questionID+' .enp-question-image-upload').hide();
        $('#enp-question--'+questionID+' .enp-question-image-upload').after(waitSpinner('enp-image-upload-wait'));
    }

    function waitSpinner(waitClass) {
        return '<div class="spinner '+waitClass+'"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
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

        // It's the first message in the array, so it'll output "Image Uploaded for Question #..."
        // add the value for this question in the input field
        $('#enp-question--'+questionID+' .enp-question-image__input').val(question.question_image);

        // clone the image container
        newImageContainer = $('#enp-question--questionTemplateID .enp-question-image__container').clone();

        imageFile = question.question_image;
        // get the 580 wide one
        imageFile = imageFile.replace(/-original/g, '580w');

        // build the imageURL
        imageURL = quizCreate.quiz_image_url + $('#enp-quiz-id').val() + '/' + questionID + '/' + imageFile;

        // insert the image
        newImageContainer.prepend('<img class="enp-question-image enp-question-image" src="'+imageURL+'" alt="'+question.question_image_alt+'"/>');
        // change the delete button value
        changeQuestionTemplateVal($('.enp-button__question-image-delete', newImageContainer), questionID);
        // add it in to the question
        $('#enp-question--'+questionID+' .enp-question-image__input').after(newImageContainer);

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

        $('#enp-question--'+questionID+' .enp-question-image__container').remove();
        // clear the input
        $('#enp-question--'+questionID+' .enp-question-image__input').val('');

        // bring the labels back
        // it doesn't exist, so we'll have to grab it from the template and change the values
        imageLabel = $('#enp-question--questionTemplateID .enp-question-image-upload').clone();
        changeQuestionTemplateAttr(imageLabel, 'for', questionID);
        imageUpload = $('#enp-question--questionTemplateID .enp-button__question-image-upload').clone();
        changeQuestionTemplateVal(imageUpload, questionID);
        imageFileSelect = $('#enp-question--questionTemplateID .enp-question-image-upload__input').clone();
        changeQuestionTemplateAttr(imageFileSelect, 'name' ,questionID);
        changeQuestionTemplateAttr(imageFileSelect, 'id' ,questionID);
        // insert the cloned elements
        imageUpload.insertAfter('#enp-question--'+questionID+' .enp-question-image__input');
        imageFileSelect.insertAfter('#enp-question--'+questionID+' .enp-question-image__input');
        imageLabel.insertAfter('#enp-question--'+questionID+' .enp-question-image__input');

        imageFile = question.question_image;
        console.log('image deleted '+imageFile);

    }


    $('document').on('click', '.enp-question-image-upload', function() {
        imageLabel = $(this);
        imageInput = imageLabel.siblings('.enp-question-image__input');
        imageSubmit = imageLabel.siblings('.enp-button__question-image-upload');
        oldImageSubmit = imageSubmit.val();
        imageInput.trigger('click'); // bring up file selector
    });

    $(document).on('change', '.enp-question-image-upload__input',  function() {
        console.log('the image to be uploaded is'+$(this).val());
        imageSubmit = $(this).siblings('.enp-button__question-image-upload');
        imageSubmit.trigger('click');
    });

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

    // for working with cloned jQuery objects
    function findReplaceAttr(obj, attr, pattern, replace) {
        // create the new attribute value
        newAttrVal = obj.attr(attr).replace(pattern, replace);
        // set the new attribute value
        obj.attr(attr, newAttrVal);
    }
});
