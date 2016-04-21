jQuery( document ).ready( function( $ ) {// Functions and stuff!
// submit quiz
$(document).on('click', '.enp-option__label', function() {
    $('.enp-question__submit').trigger('click');
});
});