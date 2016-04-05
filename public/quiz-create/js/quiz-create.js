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
        console.log('keyup');
        // get the value of the textarea we're typing in
        question_title = $(this).val();
        // find the accordion header it goes with and add in the title
        $(this).closest('.enp-question-content').prev('.enp-accordion-header').text(question_title);

    });

    // ajax submission
    $(document).on('click', '.enp-quiz-submit', function(e) {
        e.preventDefault();
        // add a click wait
        if($(this).hasClass('enp-quiz-submit--wait')) {
            console.log('waiting...');
            // TODO: animation to show stuff is happening and they should wait a sec
        } else {
            // TODO: animation to show stuff is happening and they should wait a sec
            $('.enp-quiz-message-ajax-container').append('<p class="enp-quiz-message--saving">Saving...</p>');
            // add click wait class
            $('.enp-quiz-submit').addClass('enp-quiz-submit--wait');
        }


        /*$.ajax({
                type: 'POST',
                url: quizCreate.ajax_url,
                data: {
                    'action': 'save_quiz',
                    'quiz' : $('.enp-quiz-form').serialize(),
                },
                dataType: 'json',
                success:function(json) {
                    // don't do anything!
                    console.log('success');
                    console.log(json);
                },
                error:function(json) {
                    console.log('error');
                    console.log(json);
                }
            });*/

            $.ajax( {
            type: 'POST',
    		 url  : quizCreate.ajax_url,
    		 data : {
    			action      : 'save_quiz',
                quiz : $('.enp-quiz-form').serialize(),
                quizSubmit : $(this).val(),
    		 },
    		 beforeSend : function( d ) {
    		 	console.log( 'Before send', d );
    		 }
    	} )
    		.done( function( response, textStatus, jqXHR ) {
    			// console.log( 'AJAX done', textStatus, jqXHR, jqXHR.getAllResponseHeaders() );
                //console.log( 'AJAX done', jqXHR.responseJSON );
    		} )
    		.fail( function( jqXHR, textStatus, errorThrown ) {
    			console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    		} )
    		.then( function( errorThrown, textStatus, jqXHR ) {
    			console.log( 'AJAX after finished' );
                console.log(jqXHR.responseJSON);
                var response = $.parseJSON(jqXHR.responseJSON);

                var userActionAction = response.user_action.action;
                var userActionElement = response.user_action.element;
                // remove Question
                if(userActionAction == 'delete' && userActionElement == 'question') {
                    removeQuestion(response.user_action.details.question_id);
                } else if(userActionAction == 'delete' && userActionElement == 'mc_option') {
                    removeMCOption(response.user_action.details.mc_option_id);
                } else if(userActionAction == 'add' && userActionElement == 'mc_option') {
                    // get the new inserted mc_option_id
                    new_mcOptionID = getNewMCOptionID(response.user_action.details.question_id, response.question);
                    addMCOption(new_mcOptionID, response.user_action.details.question_id);
                }
                // show ajax messages
                displayMessages(response.message);
    		} )
            .always(function() {
                $('.enp-quiz-submit').removeClass('enp-quiz-submit--wait');
                $('.enp-quiz-message--saving').remove();
            });
    });


    // set-up our ajax response container
    $('#enp-quiz').append('<section class="enp-quiz-message-ajax-container"></section>');
    // Loop through messages and display them
    // Show success messages
    function displayMessages(message) {
        // loop through success messages
        for(var success_i = 0; success_i < message.success.length; success_i++) {
            appendMessage(message.success[success_i], 'success');
        }

        // Show error messages
        for(var error_i = 0; error_i < message.error.length; error_i++) {
            appendMessage(message.error[error_i], 'error');
        }
    }

    // append ajax response messages
    function appendMessage(message, status) {
        responseTime = event.timeStamp;
        $('.enp-quiz-message-ajax-container').append('<div class="enp-quiz-message enp-quiz-message--ajax enp-quiz-message--'+status+' enp-container enp-message-'+responseTime+'"><ul class="enp-message__list enp-message__list--'+status+'">'+message+'</ul></div>');

        $('.enp-message-'+responseTime).delay(3500).fadeOut(function(){
            $('.enp-message-'+responseTime).fadeOut();
        });
    }

    function removeQuestion(questionID) {
        // remove the button
        accordionButton = $('#enp-question--'+questionID).prev('.enp-accordion-header');
        accordionButton.addClass('enp-question--remove').delay(300).slideUp(function() {
            accordionButton.remove();
        });
        $('#enp-question--'+questionID).addClass('enp-question--remove').slideUp(function() {
            $('#enp-question--'+questionID).remove();
        });
        // reindex the Question array
    }

    function removeMCOption(mcOptionID) {
        // remove the button
        $('#enp-mc-option--'+mcOptionID).addClass('enp-mc-option--remove').slideUp(function() {
            $('#enp-mc-option--'+mcOptionID).remove();
        });
        // reindex the mcOption array
    }

    // clone question, clear values, delete mc_options except one, add questionID, add MC option ID
    function addQuestion(questionID) {
        //

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
            console.log('searching...');
            var inputName = $(this).prop('name');
            var pattern = /\[mc_option\]\[(.*?)\]/;
            var index = inputName.match(pattern)[1];
            var new_index = parseInt(index)+1;
            new_inputName = inputName.replace(pattern, '[mc_option]['+new_index+']');
            $(this).attr('name', new_inputName);
            console.log(new_inputName);
        });

        // increase the counter in the array
        console.log(new_mcOption.html());
        // Finally! insert it
        $(addMCOptionButton).before(new_mcOption);
    }

    // find the newly inserted mc_option_id
    function getNewMCOptionID(questionID, question) {
        for(var i = 0; i < question.length; i++) {
            // loop through the questions and get the one we want
            // then get the id of the newly inserted mc_option
            if(question[i].question_id == questionID) {
                // now loop the mc options
                for(var mc_option_i = 0; mc_option_i < question[i].mc_option.length; mc_option_i++) {
                    if(question[i].mc_option[mc_option_i].action === 'insert') {
                        // here's our ID!
                        var new_mcOptionID = question[i].mc_option[mc_option_i].mc_option_id;
                        return new_mcOptionID;
                    }

                }
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
