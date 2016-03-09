$(document).ready(function() {
    // add hidden to all questions by default
    $('.question-fields, .accordion-title').hide().addClass('question-fields--hidden');

    if($('button.preview-quiz').length) {
        // set preview state as disabled until saved
        $('button.preview-quiz').addClass('preview-quiz--disabled');
    }

    // disable Links
    $(document).on('click', '.preview-quiz--disabled, .quiz-breadcrumbs__link--disabled', function(e) {
        e.preventDefault();
    });


    $('.add-question').click(function(e){
        e.preventDefault();

        question = $('.question-fields:eq(0)');

        if($('.preview-quiz--disabled').length) {
            $('.preview-quiz').removeClass('preview-quiz--disabled');
            $('.quiz-breadcrumbs__link--preview').removeClass('quiz-breadcrumbs__link--disabled');
        }

        // just the first time the add question button is clicked.
        if(question.hasClass('no-questions-empty-state')) {
            $('.question-fields, .accordion-title').show();
            // hide the title
            question.removeClass('no-questions-empty-state');
            $('.question-accordion').removeClass('no-questions-empty-state');
            $('.question-fields').removeClass('question-fields--hidden').addClass('question-fields--active');
            $('.question-title-field', question).focus();

            // change wording on add question button
            $('.add-question-text', this).text('Add Another Question');
        } else {
            // this is the second or greater question, so we can process it differently

            // clone the first question on the form
            var new_question = question.clone();
            // clear the values
            $('input[type="text"], textarea', new_question).val('');
            // remove correct answer
            $('.mc-create__wrap', new_question).removeClass('correct-answer');

            // hide the others
            $('.question-fields').removeClass('question-fields--active').addClass('question-fields--hidden');

            new_question.insertAfter($('.question-fields:last'));
            $('.question-accordion:eq(0)').clone().text('').insertBefore($('.question-fields:last'));

            // fade the new one in
            $('.question-fields:last').addClass('question-fields--active').removeClass('question-fields--hidden');
            $('.question-fields:last .question-title-field').focus();
        }
    });

    $(document).on('click', '.question-accordion', function() {
        this_question_section = $(this).next('.question-fields');

        if(!this_question_section.hasClass('question-fields--active')) {
            $('.question-fields').removeClass('question-fields--active').addClass('question-fields--hidden');
            this_question_section.addClass('question-fields--active').removeClass('question-fields--hidden');
            $('.question-title-field', this_question_section).focus();
        } else {
            this_question_section.removeClass('question-fields--active').addClass('question-fields--hidden');
        }
    });


    $(document).on('keyup', '.question-title-field', function() {
        // figure out which one we're on.
        question_title = $(this).val();

        $(this).closest('.question-fields').prev().text(question_title);

    });


    $(document).on('keyup', '.quiz-title', function() {
        // figure out which one we're on.
        quiz_title = $(this).val();

        $('.quiz-nav__quiz-title').text(quiz_title);
    });


    $(document).on('click', '.mc-create__input__delete', function() {
        this_input = $(this).parent('.mc-create__wrap');
        this_input.slideUp(350, function() {this_input.remove();});
        // count the others
        if(this_input.siblings('.mc-create__wrap').length === 2) {
            // hide the delete icon
            mc_wrap = this_input.parent('.mc-create');
            $('.mc-create__input__delete', mc_wrap).hide('');
        }
    });

    $(document).on('click', '.mc-create__add-option', function(e) {
        e.preventDefault();
        // get the parent mc-create wrapper
        mc_wrap = $(this).parent('.mc-create');
        // get the previous option
        prev_option = $(this).prev('.mc-create__wrap');
        mc_option_clone = prev_option.clone();
        mc_option_clone.removeClass('correct-answer');
        $('.mc-create__input', mc_option_clone).val('').attr('placeholder', '');
        mc_option_clone.insertAfter(prev_option);

        // give focus to the one you just created
        $('.mc-create__wrap:last .mc-create__input', mc_wrap).focus();

        if($(this).siblings('.mc-create__wrap').length > 2) {
            // hide the delete icon
            $('.mc-create__input__delete', mc_wrap).show('');
        }
    });

    $(document).on('click', '.mc-create__input__answer', function() {
        $(this).parent().siblings('.mc-create__wrap').removeClass('correct-answer');
        $(this).parent().addClass('correct-answer');
    });
});
