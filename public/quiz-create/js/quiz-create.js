$(document).ready(function() {
    // ready the questions as accordions
    $('.enp-question-content').each(function() {
        var accordion,
            question_title,
            question_content;

        // get the value for the title
        question_title = $('enp-question-title__textarea').val();
        // if it's empty, set it as an empty string
        if(question_title === undefined) {
            question_title = '';
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

});
