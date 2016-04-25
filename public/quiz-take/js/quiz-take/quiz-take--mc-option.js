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

/**
* Remove the MC Option from view by adding a class
* @param obj (jQuery object)
*/
function removeMCOption(obj) {
    if(!obj.hasClass('enp-option__input--incorrect-clicked')) {
        obj.addClass('enp-option__input--slide-hide');
    }
}

/**
* Highlight the correct MC Option by adding a class
* @param obj (jQuery object)
*/
function showCorrectMCOption(obj) {
    obj.addClass('enp-option__input--correct');
}

/**
* Attach data to the MC Options that lets us know if the
* mc option is correct or incorrect
* @param questionJSON (JSON String) Question level JSON
*/
function bindMCOptionData(questionJSON) {
    if(questionJSON.question_type === 'mc') {
        for(var prop in questionJSON.mc_option) {
            mc_option_id = questionJSON.mc_option[prop].mc_option_id;
            mc_option_correct = questionJSON.mc_option[prop].mc_option_correct;
            $('#enp-option__'+mc_option_id).data('correct', mc_option_correct);
        }
    }
}