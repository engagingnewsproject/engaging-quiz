jQuery( document ).ready( function( $ ) {
    // ready the questions as accordions
    $('.enp-question-content').each(function() {
        var accordion,
            question_title,
            question_content;

        // get the value for the title
        question_title = $('.enp-question-title__textarea', this).val();
        // if it's empty, set it as an empty string
        if(question_title === undefined || question_title === '') {
            question_title = 'Untitled';
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
    //$('.enp-question-image-alt__input, .enp-question-image-alt__label, .enp-button__question-image-upload, .enp-question-image-upload__input').hide();
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


        //var quizForm = new FormData($('.enp-quiz-form'));
        var quizForm = document.getElementById("enp-quiz-create-form");
        var fd = new FormData(quizForm);
        //var file = $(document).find('input[type="file"]');
        //var caption = $(this).find('input[name=img_caption]');
        //var individual_file = file[0].files[0];

        fd.append('enp-quiz-submit', userAction);
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
            // remove image
            else if(userActionAction == 'delete' && userActionElement == 'question_image') {
                // check to see if the action was completed
                questionID = response.user_action.details.question_id;
                questionResponse = checkQuestionSaveStatus(questionID, response.question);
                if(questionResponse !== false && questionResponse.action === 'update' && questionResponse.status === 'success') {
                    removeQuestionImage(questionID);
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
        $('.enp-quiz-message-ajax-container').append('<p class="enp-quiz-message--saving">Saving...</p>');
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
        // for(var success_i = 0; success_i < message.success.length; success_i++) {
            appendMessage(message.success[0], 'success');
        // }

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

        // append question deleted message
        appendMessage('Question deleted.', 'success');

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

        // just temporarily so the CSS won't hide it
        newAccordionHeader.attr('id', 'enp-question--newQuestionTemplateID__accordion-header');
        newQuestion.attr('id', 'enp-question--newQuestionTemplateID');

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

        // change the accordion header ID
        changeQuestionTemplateAttr(accordionHeader, 'id', questionID, /newQuestionTemplateID/);
        // change the question classes/ids
        changeQuestionTemplateAttr(question, 'id', questionID, /newQuestionTemplateID/);
        // change the question id value in the hidden input
        changeQuestionTemplateVal($('.enp-question-id', question), questionID);
        // change the delete button value
        changeQuestionTemplateVal($('.enp-question__button--delete', question), questionID);
        // change the image upload label for
        changeQuestionTemplateAttr($('.enp-question-image-upload', question), 'for', questionID);
        // change the image upload input id
        changeQuestionTemplateAttr($('.enp-question-image-upload__input', question), 'id', questionID);
        changeQuestionTemplateAttr($('.enp-question-image-upload__input', question), 'name', questionID);
        changeQuestionTemplateVal($('.enp-button__question-image-upload', question), questionID);
        // question type for/id
        // slider
        changeQuestionTemplateAttr($('.enp-question-type__input--slider', question), 'id', questionID);
        changeQuestionTemplateAttr($('.enp-question-type__label--slider', question), 'for', questionID);
        // mc option
        changeQuestionTemplateAttr($('.enp-question-type__input--mc', question), 'id', questionID);
        changeQuestionTemplateAttr($('.enp-question-type__label--mc', question), 'for', questionID);

        changeQuestionTemplateVal($('.enp-mc-option__button--correct', question), questionID);
        changeQuestionTemplateVal($('.enp-mc-option__add', question), questionID);

        // change the default MCOptionIDs
        addMCOption(mcOptionID, questionID);

        // change the question array iterator
        // setup question index and regex
        question_index = getQuestionIndex(questionID);
        question_index_pattern = /enp_question\[questionCounterTemplate\]/;
        // reindex/change the input names
        $('input, textarea', question).each(function () {
            var inputName = $(this).prop('name');
            // change the index of the form array for the question
            new_inputName = inputName.replace(question_index_pattern, 'enp_question['+question_index+']');
            $(this).attr('name', new_inputName);
        });
        // add the mc option

    }

    function changeQuestionTemplateAttr(obj, attr, questionID, pattern) {
        // set default pattern if not passed in the function
        pattern = typeof pattern !== 'undefined' ? pattern : /questionTemplateID/;
        // create the new attribute value
        newAttrVal = obj.attr(attr).replace(pattern, questionID);
        // set the new attribute value
        obj.attr(attr, newAttrVal);
    }

    function changeQuestionTemplateVal(obj, questionID, pattern) {
        // set default pattern if not passed in the function
        pattern = typeof pattern !== 'undefined' ? pattern : /questionTemplateID/;
        // create the new value
        newObjVal = obj.val().replace(pattern, questionID);
        // set the new value
        obj.val(newObjVal);
    }

    function changeMCOptionTemplateAttr(obj, attr, mcOptionID, pattern) {
        // set default pattern if not passed in the function
        pattern = typeof pattern !== 'undefined' ? pattern : /mcOptionTemplateID/;
        // create the new attribute value
        newObjVal = obj.attr(attr).replace(pattern, mcOptionID);
        // set the new attribute value
        obj.attr(attr, newObjVal);
    }

    function changeMCOptionTemplateVal(obj, mcOptionID, pattern) {
        // set default pattern if not passed in the function
        pattern = typeof pattern !== 'undefined' ? pattern : /mcOptionTemplateID/;
        // create the new value
        newObjVal = obj.val().replace(pattern, mcOptionID);
        // set the new value
        obj.val(newObjVal);
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

    function temp_removeQuestionImage(questionID) {
        $('#enp-question--'+questionID+' .enp-question-image__container').addClass('enp-question__image--remove');
    }

    function temp_unsetRemoveQuestionImage(questionID) {
        $('#enp-question--'+questionID+' .enp-question-image__container').removeClass('enp-question__image--remove');
    }

    function removeQuestionImage(questionID) {
        $('#enp-question--'+questionID+' .enp-question-image__container').remove();
        // clear the input
        $('#enp-question--'+questionID+' .enp-question-image__input').val('');
        // bring the labels back
        if($('#enp-question--'+questionID+' .enp-question-image-upload').length > 0) {
            // this part of the template is there already, so just fade it in
            $('#enp-question--'+questionID+' .enp-question-image-upload').fadeIn();
        } else {
            console.log('could not find');
            // it doesn't exist, so we'll have to grab it from the template and change the values
            imageLabel = $('#enp-question--questionTemplateID .enp-question-image-upload').clone();
            changeQuestionTemplateAttr(imageLabel, 'for', questionID);
            imageUpload = $('#enp-question--questionTemplateID .enp-button__question-image-upload').clone();
            changeQuestionTemplateVal(imageUpload, questionID);
            // insert the cloned elements
            imageLabel.insertAfter('#enp-question--'+questionID+' .enp-question-image__input');
            imageUpload.insertAfter('#enp-question--'+questionID+' .enp-question-image__input');

        }
        // It's the first message in the array, so it'll output "Image Deleted for Question #..."
        // appendMessage('Image deleted.', 'success');
    }

    // Image uploader
    // Uploading files
    var file_frame;

      $('.enp-question-image-upload').live('click', function( event ){

        event.preventDefault();
        imageLabel = $(this);
        imageInput = imageLabel.siblings('.enp-question-image__input');
        imageSubmit = imageLabel.siblings('.enp-button__question-image-upload');
        // If the media frame already exists, reopen it.
        if ( file_frame ) {
          file_frame.open();
          return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: 'Upload Image',
          button: {
            text: 'Add Image to Question',
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = file_frame.state().get('selection').first().toJSON();
          console.log(attachment);
          // add the file path to the hidden input
          imageInput.val(attachment.url);
          // add the alt text
          imageLabel.siblings('.enp-question-image-alt__input').val(attachment.alt);
          // trigger a click on the save button
          imageSubmit.trigger('click');
          // Put our uploaded image in place of the image label
          // change the value of the input path
          parseURL = imageInput.val().split('/').pop();
          // not sure why we need two dashes here...
          parseURL = parseURL.replace(/\.([^.]+)$/, '--original.$1');
          console.log(parseURL);
          imageInput.val(parseURL);

          // clone the image container
          newImageContainer = $('#enp-question--questionTemplateID .enp-question-image__container').clone();
          // get the question id
          questionID = imageLabel.attr('for').replace('enp-question-image-upload-', '');
          // insert the image
          newImageContainer.prepend('<img class="enp-question-image enp-question-image" src="'+attachment.url+'" alt="'+attachment.description+'"/>');
          // change the delete button value
          changeQuestionTemplateVal($('.enp-button__question-image-delete', newImageContainer), questionID);

          imageLabel.before(newImageContainer);
          // hide the label
          imageLabel.hide();
        });

        // Finally, open the modal
        file_frame.open();
    });
});
