jQuery( document ).ready( function( $ ) {

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



    // Clickable things that don't work yet
    $(document).on('click', '.add-image, .a-b-test-create', function(e) {
        e.preventDefault();
        alert('This does not work, but I am SO glad you successfully clicked it :)');
    });


});
