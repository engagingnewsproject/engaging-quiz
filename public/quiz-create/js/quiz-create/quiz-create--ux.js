/*
* General UX interactions to make a better user experience
*/

// MC option image: clicking the Add Image icon triggers the hidden file input (trigger is in right-actions, file input in same option)
$(document).on('click', '.enp-mc-option-image-upload__trigger', function(e) {
	e.preventDefault();
	$(this).closest('.enp-mc-option').find('.enp-mc-option-image-upload__input').trigger('click');
});

// MC option image: show preview when a file is selected (before submit); hide "Add Image" link, show clear button
$(document).on('change', '.enp-mc-option-image-upload__input', function() {
	var $input = $(this);
	var $upload = $input.closest('.enp-mc-option-image-upload');
	var $preview = $upload.find('.enp-mc-option-image-preview');
	var file = this.files && this.files[0];

	if (!$preview.length) {
		return;
	}

	// Revoke previous object URL to avoid memory leaks
	var oldUrl = $preview.data('object-url');
	if (oldUrl) {
		URL.revokeObjectURL(oldUrl);
		$preview.removeData('object-url');
	}

	$preview.empty().addClass('enp-mc-option-image-preview--hidden');

	if (file && file.type.indexOf('image/') === 0) {
		var url = URL.createObjectURL(file);
		var $img = $('<img class="enp-mc-option-image enp-mc-option-image--preview" alt="">').attr('src', url);
		var $clearBtn = $('<button type="button" class="enp-mc-option-image-preview__clear" aria-label="Remove image"><svg class="enp-icon enp-icon--delete"><use xlink:href="#icon-delete"></use></svg></button>');
		$preview.data('object-url', url).append($img).append($clearBtn).removeClass('enp-mc-option-image-preview--hidden');
	}
});

// MC option image: clear preview (remove file, hide preview, show "Add Image" link again)
$(document).on('click', '.enp-mc-option-image-preview__clear', function(e) {
	e.preventDefault();
	var $preview = $(this).closest('.enp-mc-option-image-preview');
	var $upload = $preview.closest('.enp-mc-option-image-upload');
	var $input = $upload.find('.enp-mc-option-image-upload__input');

	var oldUrl = $preview.data('object-url');
	if (oldUrl) {
		URL.revokeObjectURL(oldUrl);
		$preview.removeData('object-url');
	}
	$input.val('');
	$preview.empty().addClass('enp-mc-option-image-preview--hidden');
});

// set titles as the values are being typed
$(document).on('keyup', '.enp-question-title__textarea', function() {
    // get the value of the textarea we're typing in
    question_title = processAccordionTitle($(this).val())
    
    // find the accordion header it goes with and add in the title
    $(this).closest('.enp-question-content').prev('.enp-accordion-header').find('.enp-accordion-header__title').text(question_title);
});


// a click on Preview or Publish nav just clicks the preview button instead
$(document).on('click', '.enp-quiz-breadcrumbs__link--preview, .enp-quiz-breadcrumbs__link--publish', function(e) {
    e.preventDefault();
    $('.enp-btn--next-step').trigger('click');
});


function hideSaveButton() {
    $('.enp-quiz-form__save, .enp-btn--next-step').hide();
}

function showSaveButton() {
    $('.enp-quiz-form__save').show().addClass('enp-quiz-form__save--reveal');
    $('.enp-btn--next-step').show().addClass('enp-btn--next-step--reveal');
    $('.enp-quiz-breadcrumbs__link--preview').removeClass('enp-quiz-breadcrumbs__link--disabled');
}