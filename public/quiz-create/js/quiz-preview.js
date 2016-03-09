$(document).ready(function() {
    // quiz preview page
    $(document).on('change', 'input[name="show-title"]', function(){
        if( $(this).val() === 'title' ) {
            $('.take-quiz-title').show();
        } else {
            $('.take-quiz-title').hide();
        }
    });

    $(document).on('click', '.mc-answer', function(e) {
        e.preventDefault();
        if($('.take-quiz-question').hasClass('take-quiz-question--answer-clicked')) {
            // they clicked again. THAT'S NOT COOL
            return false;
        } else {
            $('.take-quiz-question').addClass('take-quiz-question--answer-clicked');
        }

        if($(this).hasClass('correct')) {
            // the answer is right!
            $(this).addClass('mc-answer--correct-clicked');
            $(this).siblings('.mc-answer').addClass('slide-hide');
            $('.take-quiz__answer-explanation').addClass('answer-explanation--correct');
            $('.answer-explanation__title').text('Correct');
            $('.answer-explanation__percentage').text('62% got this right');
        } else {
            $(this).addClass('mc-answer--incorrect-clicked');
            $(this).siblings('.correct').addClass('mc-answer--correct');
            //remove the incorrect options
            $(this).siblings('.incorrect').addClass('slide-hide');

            $('.take-quiz__answer-explanation').addClass('answer-explanation--incorrect');
            $('.answer-explanation__title').text('Incorrect');
            $('.answer-explanation__percentage').text('38% got this wrong');
        }

        $('.take-quiz__answer-explanation').addClass('answer-explanation--show');
        $('.answer-explanation__header').focus();

    });

    var nextQuestion = $('.take-quiz-question').clone();
    nextQuestion.addClass('on-deck last-question');
    // replace the Text
    $('.question', nextQuestion).text('Is this the second question of your quiz?');
    $('.next-question__btn-text', nextQuestion).text('View Results');
    $('.take-quiz__results').hide();


    $(document).on('click', '.next-question', function(e) {
        e.preventDefault();
        // remove the question
        $('.take-quiz-question').addClass('remove-question');

        if(!$('.take-quiz-question--answer-clicked').hasClass('last-question')) {
            // insert the next one
            nextQuestion.addClass('on-deck--arrive');
            $('.take-quiz').append(nextQuestion);
            //$('.take-quiz-question:eq(0)').hide();
            // increase the progress bar
            $('.take-quiz-progress-bar').css('width','98%');
            $('.take-quiz-question-count').text('2/2');

            // set a timeout to let our animation complete, then remove the question
            removeQuestionTimeoutID = window.setTimeout(removeAnsweredQuestion, 300);
            // get the results ready
            $('take-quiz__results').addClass('on-deck');
        } else {
            // bring in our
            $('.take-quiz__results').show().addClass('on-deck--arrive');

            $('.take-quiz-progress-bar').css('width','100%');
            $('.take-quiz-question-count').text('1/2 correct');

            // add the resetOffset to take it to 0%
            $('#score-circle__path').attr('class', 'resetOffset');
            // add the animateScore after a slight delay so the animation comes in
            animateScoreID = window.setTimeout(animateScore, 250);

        }

    });

    // function for our timeout to remove the answered question
    function removeAnsweredQuestion() {
        $('.take-quiz-question--answer-clicked:first').remove();
    }

    // function for our timeout to animate the svg percentage correct
    function animateScore() {
        $('#score-circle__path').attr('class', 'setOffset');
    }


});
