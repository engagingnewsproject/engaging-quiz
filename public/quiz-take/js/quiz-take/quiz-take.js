// turn on mustache/handlebars style templating
_.templateSettings = {
  interpolate: /\{\{(.+?)\}\}/g
};
// Templates
var mcOptionTemplate = _.template($('#mc_option_template').html());
var questionTemplate = _.template($('#question_template').html());
var questionImageTemplate = _.template($('#question_image_template').html());
var questionExplanationTemplate = _.template($('#question_explanation_template').html());


/*
* MC OPTION
*/
// set-up
attach_mc_option_data(question_json);

// attach data to mc options on load
function attach_mc_option_data(question_json) {
    if(question_json.question_type === 'mc') {
        for(var prop in question_json.mc_option) {
            mc_option_id = question_json.mc_option[prop].mc_option_id;
            mc_option_correct = question_json.mc_option[prop].mc_option_correct;
            $('#enp-option__'+mc_option_id).data('correct', mc_option_correct);
        }
    }
}

/**
* Find all the mc options in a container and tell us which is the correct one
* @param container (obj) wrapper for the inputs to search
* @param callback (function) Something to do with the correct one found
* @return input object that is correct
*/
function locateCorrectMCOption(container, callback) {
    var correct;
    $('.enp-option__input', container).each(function(e, obj) {
        if($(this).data('correct') === '1') {
            correct =  $(this);
            callback($(this));
            return false;
        }
    });
    return correct;
}


/**
* Find all the mc options in a container and tell us which are incorrect
* @param container (obj) wrapper for the inputs to search
* @param callback (function) Something to do with the incorrect one found
* @return array of MC Input objects that are incorrect
*/
function locateIncorrectMCOptions(container, callback) {
    var incorrect;
    $('.enp-option__input', container).each(function(e, obj) {
        if($(this).data('correct') === '0') {
            incorrect =  $(this);
            callback($(this));
        }
    });
    return incorrect;
}

function removeMCOption(obj) {
    obj.addClass('enp-option__input--slide-hide');
}


/**
* On click of a selection,
* 1. Show the right one
* 2. Slide out incorrect ones (other than the one clicked)
* 3. Show the explanation
* 4. Send the answer to the server
*/
$(document).on('click', '.enp-option__label', function(e){

    var thisMCInput = $(this).prev('.enp-option__input');
    var correct = thisMCInput.data('correct');
    // set-up the question container class, remove unanswered class
    $('.enp-question__container').addClass('enp-question__container--explanation').removeClass('enp-question__container--unanswered');
    // check if it's correct or not
    if(correct === '1') {
        // it's right!
        thisMCInput.addClass('enp-option__input--correct-clicked');
    } else {
        // it's wrong :( :( :(
        thisMCInput.addClass('enp-option__input--incorrect-clicked');
        // show the correct one
        correctInput = locateCorrectMCOption($('.enp-question__fieldset'));
        console.log(correctInput);
        correctInput.addClass('enp-option__input--correct');
    }
    // remove all the ones that are incorrect that DON'T Have incorrect-clicked on them
    locateIncorrectMCOptions($('.enp-question__fieldset'), removeMCOption);

});
