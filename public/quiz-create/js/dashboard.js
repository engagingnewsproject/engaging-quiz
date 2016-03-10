jQuery( document ).ready( function( $ ) {
    $('.view-toggle').click(function() {
        if(!$(this).hasClass('active')) {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            var quizList = $(this).parent().parent().next('.quiz-list');
            if( quizList.hasClass('flex-list') ) {
                quizList.removeClass('flex-list');
            } else {
                quizList.addClass('flex-list');
            }
        }
    });
});
