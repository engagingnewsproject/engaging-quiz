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
        accordion = {title: question_title, content: question_content};
        //returns an accordion object with the header object and content object
        accordion = enp_accordion__create_headers(accordion);
        // set-up all the accordion classes and start classes (so they're closed by default)
        enp_accordion__setup(accordion);
    });

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
        $.ajax( {
            type: 'POST',
             url  : quizCreate.ajax_url,
             data : {
                action      : 'save_quiz',
                quiz : $('.enp-quiz-form').serialize(),
                quizSubmit : userAction,
             },
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
            // remove Question
            else if(userActionAction == 'delete' && userActionElement == 'question') {
                // check to see if the action was completed
                questionID = response.user_action.details.question_id;
                questionResponse = checkQuestionSaveStatus(questionID, response.question);
                if(questionResponse !== undefined && questionResponse.action === 'update' && questionResponse.status === 'success') {
                    removeQuestion(questionID);
                } else {
                    temp_unsetRemoveQuestion(questionID);
                }

            } else if(userActionAction == 'delete' && userActionElement == 'mc_option') {
                // check to see if the action was completed
                var mcOptionID = response.user_action.details.mc_option_id;
                mcOptionResponse = checkMCOptionSaveStatus(questionID, response.question);
                removeMCOption(mcOptionID);
            } else if(userActionAction == 'add' && userActionElement == 'mc_option') {
                // get the new inserted mc_option_id
                questionID = response.user_action.details.question_id;
                var new_mcOptionID = getNewMCOptionID(questionID, response.question);
                addMCOption(new_mcOptionID, questionID);
            } else if(userActionAction == 'set_correct' && userActionElement == 'mc_option') {
                // set the correct one
                setCorrectMCOption(response.user_action.details.mc_option_id, response.user_action.details.question_id);
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
        if(userAction.indexOf('question--delete') > -1) {
            // match the number for the ID
            pattern = /question--delete-/g;
            var questionID = userAction.replace(pattern, '');
            temp_removeQuestion(questionID);
        }
        // deleting a mc option
        else if(userAction.indexOf('mc-option--delete') > -1) {
            // match the number for the ID
            pattern = /mc-option--delete-/g;
            var mcOptionID = userAction.replace(pattern, '');
            temp_removeMCOption(mcOptionID);
        } else {

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

    // clone question, clear values, delete mc_options except one, add questionID, add MC option ID
    function addQuestion(questionID) {
        //

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

    // clone mc_option, clear value, remove "correct answer" if set, add MC option ID
    function addMCOption(new_mcOptionID, questionID) {

        var new_mcOption,
            old_mcOptionId,
            addMCOptionButton;

        // get the clicked button
        addMCOptionButton = $('#enp-question--'+questionID+' .enp-mc-option--add');
        // get closest MC Option and clone it
        new_mcOption = $(addMCOptionButton).prev('.enp-mc-option').clone();

        old_mcOptionId = $('.enp-mc-option-id', new_mcOption).val();
        // remove correct class (if it has it)
        if(new_mcOption.hasClass('enp-mc-option--correct')) {
            new_mcOption.removeClass('enp-mc-option--correct');
        }
        // clear the value
        $('.enp-mc-option__input', new_mcOption).val('');
        // change the id
        $('.enp-mc-option-id', new_mcOption).val(new_mcOptionID);
        // change the delete button value
        $('.enp-mc-option__button--delete', new_mcOption).val('mc-option--delete-'+new_mcOptionID);
        // change the correct button value
        $('.enp-mc-option__button--correct', new_mcOption).val('mc-option--correct__question-'+questionID+'__mc-option-'+new_mcOptionID);
        // change the ID
        $(new_mcOption).attr('id', 'enp-mc-option--'+new_mcOptionID);
        // change the index of the form array
        $('input', new_mcOption).each(function () {
            var inputName = $(this).prop('name');
            var pattern = /\[mc_option\]\[(.*?)\]/;
            var index = inputName.match(pattern)[1];
            var new_index = parseInt(index)+1;
            new_inputName = inputName.replace(pattern, '[mc_option]['+new_index+']');
            $(this).attr('name', new_inputName);
        });

        // Insert it
        $(addMCOptionButton).before(new_mcOption);
        // give it focus!
        $('#enp-mc-option--'+new_mcOptionID+' .enp-input').focus();
    }

    // find the newly inserted mc_option_id
    function getNewMCOptionID(questionID, question) {
        for (var prop in question) {
            console.log(question[prop]);
            // loop through the questions and get the one we want
            // then get the id of the newly inserted mc_option
            if(parseInt(question[prop].question_id) === parseInt(questionID)) {
                console.log('found question.');
                // now loop the mc options
                for(var mc_option_prop in question[prop].mc_option) {
                    console.log(question[prop].mc_option[mc_option_prop]);
                    if(question[prop].mc_option[mc_option_prop].action === 'insert') {
                        // here's our new mc option ID!
                        return question[prop].mc_option[mc_option_prop].mc_option_id;
                    }

                }
            }
        }
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

    }

    // Image uploader
    // Uploading files
    /*var file_frame;

      $('.enp-question-image-upload').live('click', function( event ){

        event.preventDefault();

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
          // Do something with attachment.id and/or attachment.url here
        });

        // Finally, open the modal
        file_frame.open();
    });*/
});
