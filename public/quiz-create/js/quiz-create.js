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
    			console.log( 'AJAX done', textStatus, jqXHR, jqXHR.getAllResponseHeaders() );
    		} )
    		.fail( function( jqXHR, textStatus, errorThrown ) {
    			console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    		} )
    		.then( function( jqXHR, textStatus, errorThrown ) {
    			console.log( 'AJAX after finished', jqXHR, textStatus, errorThrown );
    		} );
    });


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
