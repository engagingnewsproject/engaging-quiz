$(document).ready(function() {

    $(document).scroll(function() {
        var topDist = $(document).scrollTop();
        var controlBar = $('.control-bar');
        if(topDist > 32) {
            if(!controlBar.hasClass('control-bar--fixed')) {
                controlBar.addClass('control-bar--fixed');
            }
        } else if(topDist < 32) {
            if(controlBar.hasClass('control-bar--fixed')) {
                controlBar.removeClass('control-bar--fixed');
            }
        }
    });

});
