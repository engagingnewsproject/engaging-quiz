$(document).ready(function() {
    $(document).on('change', 'input[name="title-display"]', function(){
        if( $(this).val() === 'title-show' ) {
            $('.enp-quiz__title').show();
        } else {
            $('.enp-quiz__title').hide();
        }
    });

    $(document).on('click', '.enp-option__input', function(e) {
        e.preventDefault();
        if($('.enp-question__container').hasClass('enp-question__container--explanation')) {
            // they clicked again. THAT'S NOT COOL
            return false;
        } else {
            $('.enp-question__container').removeClass('enp-question__container--unanswered').addClass('enp-question__container--explanation');
        }

        if($(this).hasClass('enp-option__input--correct')) {
            // the answer is right!
            $(this).addClass('enp-option__input--correct-clicked');
            $(this).siblings('.enp-option__input').addClass('enp-option__input--slide-hide');
            $('.enp-explanation').addClass('enp-explanation--correct');
            $('.enp-explanation__title__text').text('Correct');
            $('.enp-explanation__percentage').text('62% got this right');
        } else {
            $(this).addClass('enp-option__input--incorrect-clicked');
            //remove the incorrect options
            $(this).siblings('.enp-option__input--incorrect').addClass('enp-option__input--slide-hide');

            $('.enp-explanation').addClass('enp-explanation--incorrect');
            $('.enp-explanation__title__text').text('Incorrect');
            $('.enp-explanation__percentage').text('38% got this wrong');
        }

        $('.enp-explanation').addClass('enp-explanation--show');
        $('.enp-explanation__header').focus();

    });

});
