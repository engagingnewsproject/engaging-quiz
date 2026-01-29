/**
 * MC option image expand: open image in modal.
 * Expand button is outside the label so clicking it does not select the option.
 */
$(document).on('click', '.enp-option__image-expand', function(e) {
    e.preventDefault();
    e.stopPropagation();
    // Use .attr() so hyphenated data-enp-expand-src is read correctly (jQuery .data() uses camelCase)
    var src = $(this).attr('data-enp-expand-src');
    var $modal = $('#enp-option-image-modal');
    var $img = $modal.find('.enp-option-image-modal__img');
    var alt = $(this).closest('.enp-option__image-wrap').find('.enp-option__image').attr('alt') || '';
    if (src) {
        $img.attr({ src: src, alt: alt });
        $modal.removeAttr('hidden');
    }
});

$(document).on('click', '.enp-option-image-modal__backdrop, .enp-option-image-modal__close', function() {
    $('#enp-option-image-modal').attr('hidden', 'hidden');
});

// Close modal on Escape
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#enp-option-image-modal').length && !$('#enp-option-image-modal').attr('hidden')) {
        $('#enp-option-image-modal').attr('hidden', 'hidden');
    }
});
