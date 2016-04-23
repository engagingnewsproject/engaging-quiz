jQuery( document ).ready( function( $ ) {// turn on mustache/handlebars style templating
_.templateSettings = {
  interpolate: /\{\{(.+?)\}\}/g
};
// Templates
var mcOptionTemplate = _.template($('#mc_option_template').html());
var questionTemplate = _.template($('#question_template').html());
var questionImageTemplate = _.template($('#question_image_template').html());
var questionExplanationTemplate = _.template($('#question_explanation_template').html());

// UTILITY
/**
* get a string or decimal integer and return a formatted decimal number
* @param places (int) how many decimal places you want to leave in Defaults to 0.
*/
_.reformat_number = function(number, multiplier, places) {
    if(multiplier === "") {
        multiplier = 1;
    }
    if(places === "") {
        places = 0;
    }
    number = number * multiplier;
    number = number.toFixed(places);
    return number;
};
// INIT

// load expanation template
function generateQuestionExplanation(question_json, correct, callback) {
    var question_response_percentage = question_json['question_responses_'+correct+'_percentage'];
    question_response_percentage = _.reformat_number(question_response_percentage, 100);
    explanationTemplate = questionExplanationTemplate({question_id: question_json.question_id, question_explanation: question_json.question_explanation, question_explanation_title: correct, question_explanation_percentage: question_response_percentage });
    if(typeof(callback) == "function") {
        callback(explanation);
    }
    return explanationTemplate;
}

/*
* MC OPTION INIT
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



/*
* MC OPTION
*/


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
            if(typeof(callback) == "function") {
                callback($(this));
            }
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
            if(typeof(callback) == "function") {
                callback($(this));
            }
        }
    });
    return incorrect;
}

function removeMCOption(obj) {
    if(!obj.hasClass('enp-option__input--incorrect-clicked')) {
        obj.addClass('enp-option__input--slide-hide');
    }
}

function showCorrectMCOption(obj) {
    obj.addClass('enp-option__input--correct');
}


/**
* On click of a selection,
* 1. Show the right one
* 2. Slide out incorrect ones (other than the one clicked)
* 3. Show the explanation
*/
$(document).on('click', '.enp-option__label', function(e){

    var thisMCInput = $(this).prev('.enp-option__input');
    var correct = thisMCInput.data('correct');
    // set-up the question container class, remove unanswered class
    $('.enp-question__container').addClass('enp-question__container--explanation').removeClass('enp-question__container--unanswered');
    // check if it's correct or not
    if(correct === '1') {
        correct_string = 'correct';
        // it's right!
        thisMCInput.addClass('enp-option__input--correct-clicked');
        showCorrectMCOption(thisMCInput);
    } else {
        correct_string = 'incorrect';
        // it's wrong :( :( :(
        thisMCInput.addClass('enp-option__input--incorrect-clicked');
        // show the correct one
        correctInput = locateCorrectMCOption($('.enp-question__fieldset'), showCorrectMCOption);
    }
    // remove all the ones that are incorrect that DON'T Have incorrect-clicked on them
    locateIncorrectMCOptions($('.enp-question__fieldset'), removeMCOption);

    // show the explanation
    var qeTemplate = generateQuestionExplanation(question_json, correct_string);
    $('.enp-question__submit').before(qeTemplate);

});

// 4. Click the submit button on input change
// submit the form on change
$('.enp-option__input').change(function() {
    console.log('changed');
    $('.enp-question__submit').trigger('click');
});

// 5. save the quiz on click
// AJAX save
$(document).on('click', '.enp-question__submit', function(e){
    e.preventDefault();
    url = $('.enp-question__form').attr('action');
    // add button value and name to the data since jQuery doesn't submit button value
    userAction = $(this).attr("name") + "=" + $(this).val();
    // add in a little data to let the server know the data is coming from an ajax call
    doing_ajax = 'doing_ajax=doing_ajax';
    data = $('.enp-question__form').serialize() + "&" + userAction + "&" + doing_ajax;
    console.log(data);
    $.ajax( {
        type: 'POST',
        url  : url,
        data : data,
        dataType : 'json',
    } )
    // success
    .done( quizSaveSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {
        console.log( 'AJAX after finished' );
    } )
    .always(function() {

    });
});

function quizSaveSuccess( response, textStatus, jqXHR ) {
    console.log(jqXHR.responseText);
}
});