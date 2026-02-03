/*
* What needs to happen on Load
*/

// ready the questions as accordions and add in swanky button template
$('.enp-question-content').each(function(i) {
    // set up accordions
    setUpAccordion($(this));
    // add in image upload button template if it doesn't have an image
    if($('.enp-question-image', this).length === 0) {
        $('.enp-question-image__input', this).after(questionImageUploadButtonTemplate());
    }
});



// hide descriptions
$('.enp-image-upload__label, .enp-button__question-image-upload, .enp-question-image-upload__input').hide();

// add our sliders into the templates
$('.enp-slider-options').each(function() {
    setUpSliderTemplate($(this));
});

// check if there are any questions. If there aren't, then don't show the save/preview buttons
var url = window.location.href;
var patt = new RegExp("quiz-create/new");
if(patt.test(url) === true) {
    hideSaveButton();
}

// Check if there are any error messages; highlight questions and show inline error text.
if ($('.enp-message__item--error').length !== 0) {
    var re = /Question \d+/;
    var errorsByQuestion = {};
    $('.enp-message__item--error').each(function() {
        var errorMessage = $(this).text().trim();
        var found = errorMessage.match(re);
        if (found !== null) {
            var questionNumber = found[0].replace(/Question /, '');
            var questionIndex = parseInt(questionNumber, 10) - 1;
            if (!errorsByQuestion[questionIndex]) {
                errorsByQuestion[questionIndex] = [];
            }
            errorsByQuestion[questionIndex].push(errorMessage);
        }
    });
    $.each(errorsByQuestion, function(questionIndex, messages) {
        var questionHeader = $('.enp-question-content:eq(' + questionIndex + ')').prev('.enp-accordion-header');
        if (questionHeader.length && !questionHeader.hasClass('question-has-error')) {
            questionHeader.addClass('question-has-error');
            questionHeader.find('.enp-accordion-header__error').remove();
            var errorHtml = '<span class="enp-accordion-header__error" role="alert">' +
                messages.join(' ') + '</span>';
            questionHeader.find('.enp-accordion-header__title').after(errorHtml);
        }
    });
}

// tinymce: Prevent jQuery UI dialog from blocking focusin
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
    e.stopImmediatePropagation();
  }
});

// Server-rendered success messages (e.g. after Preview redirect): auto-hide after 5 seconds.
$('.enp-quiz-message--success').not('.enp-quiz-message--ajax').delay(5000).fadeOut(function() {
    $(this).remove();
});

// get each question container
$theQuestions = $('.enp-accordion-container');

// init TinyMCE for each question's Answer Explanation (addTinymce retries if tinymce not loaded yet)
$.each($theQuestions, function(i) {
    obj = getQuestionID(this);
    addTinymce(obj);
});
