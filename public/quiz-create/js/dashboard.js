/**
* On Load, remove the search button
*/
$('.enp-search-quizzes__button').addClass('enp-screen-reader-text');

/**
* On change of dropdown input, submit the form
*/
$(document).on('change', '.enp-search-quizzes__select', function() {
    $('#enp-search-quizzes').submit();
});

/**
* REMOVING FOR NOW. DON'T THINK THIS ACTUALLY ADDS ANYTHING OF VALUE
* On Load, create the list view toggle elements HTML

$('.enp-quiz-list__view').append('<svg class="enp-view-toggle enp-view-toggle__grid enp-icon"><use xlink:href="#icon-grid"><title>Grid View</title></use></svg><svg class="enp-view-toggle enp-view-toggle__list enp-icon"><use xlink:href="#icon-list"><title>List View</title></use></svg>');
// add active class initially to grid view
$('.enp-view-toggle__grid').addClass('enp-view-toggle__active');
*/

/**
* On toggle click, add active class/remove it from the other one
* and change the classes for the view

$(document).on('click', '.enp-view-toggle', function() {
    // check if it has the active class or not
    if(!$(this).hasClass('enp-view-toggle__active')) {
        // remove active class from siblings
        $(this).siblings('.enp-view-toggle').removeClass('enp-view-toggle__active');
        // add active class to itself
        $(this).addClass('enp-view-toggle__active');
        // find the corresponding list
        var quizList = $(this).parent().parent().next('.enp-dash-list');
        // check if it has the list view class, if it does, remove it, else, add it
        if( quizList.hasClass('enp-dash-list--list-view') ) {
            quizList.removeClass('enp-dash-list--list-view');
        } else {
            quizList.addClass('enp-dash-list--list-view');
        }
    }
});
*/
/**
* On load, create the button element to show/hide the dash item nav
*/
$('.enp-dash-item__nav').each(function() {
    $(this).addClass('enp-dash-item__nav--collapsible')
            .attr('aria-hidden', true)
            .before('<button class="enp-dash-item__menu-action" type="button" aria-expanded="false" aria-controls="'+$(this).attr('id')+'"><svg class="enp-dash-item__menu-action__icon enp-dash-item__menu-action__icon--bottom"><use xlink:href="#icon-chevron-down" /></svg></button>');
});

/**
* On click, show/hide the menu for a dash item
*/
$(document).on('click', '.enp-dash-item__menu-action', function() {
   var dashItem = $(this).closest('.enp-dash-item');

    if(dashItem.hasClass('enp-dash-item--menu-active')) {
        removeActiveMenuStates(dashItem);
    } else {

        // remove states from any active menu item, if there is one
       var previouslyActiveMenu = $('.enp-dash-item--menu-active');
       if(0 < previouslyActiveMenu.length ) {
           removeActiveMenuStates(previouslyActiveMenu);
       }
       // add in new active states
       addActiveMenuStates(dashItem);
       // move focus to first item in menu
       $('.enp-dash-item__nav__item:eq(0) a', dashItem).focus();
    }
});

/**
* Add the classes and attributes to show our menu item
*/
function addActiveMenuStates(dashItem) {
    // add the new active states in
    dashItem.addClass('enp-dash-item--menu-active');
    // button to activate the menu
    $('.enp-dash-item__menu-action', dashItem).attr('aria-expanded', true);
    // menu
    $('.enp-dash-item__nav', dashItem).attr('aria-hidden', false);
}

/**
* Remove the classes and attributes to show our menu item
*/
function removeActiveMenuStates(dashItem) {
    // dash item card
    dashItem.removeClass('enp-dash-item--menu-active');
    // button to activate the menu
    $('.enp-dash-item__menu-action', dashItem).attr('aria-expanded', false);
    // menu
    $('.enp-dash-item__nav', dashItem).attr('aria-hidden', true);
}


/**
* Deleting a quiz
* Process, submit, and handle the delete form submission with AJAX
*/
$('.enp-dash-item__delete').click(function(e) {
    e.preventDefault();
    // get the dash item
    var dashItem = $(this).closest('.enp-dash-item');

    // determine if we're deleting a quiz or an AB test
    // off of the button value
    var userAction = $(this).val();

    // TODO This should be an "undo", not a confirm
    if(userAction === 'delete-quiz') {
        confirmDeleteText = 'Are you sure you want to delete this quiz? This will also delete any AB Tests you have set-up with this Quiz.';
    }
    else if(userAction === 'delete-ab-test') {
        confirmDeleteText = 'Are you sure you want to delete this AB Test?';
    } else {
        // not sure what we're going to do here...
        alert('Something went wrong. Please send us an email telling us how you reached this error message');
    }

    // show the confirm message
    var confirmDelete = confirm(confirmDeleteText);

    if(confirmDelete === false) {
        return false;
    }  else {
        // they want to delete it, so let them
        // TODO This should be an "undo", not a confirm
    }

    // check if we have the click wait class already
    // add a click wait, if necessary
    if($(this).hasClass('enp-quiz-submit--wait')) {
        // be patient!
        return false;
    } else {
        setWait();
    }

    // add a little spinner to show we're working on deleting it
    deleteQuizWait(dashItem);

    var fd = deleteFormData(dashItem, userAction);

    $.ajax( {
        type: 'POST',
         url  : quizDashboard.ajax_url,
         data : fd,
         processData: false,  // tell jQuery not to process the data
         contentType: false,   // tell jQuery not to set contentType
    } )
    // success
    .done( quizDeleteSuccess )
    .fail( function( jqXHR, textStatus, errorThrown ) {
        console.log( 'AJAX failed', jqXHR.getAllResponseHeaders(), textStatus, errorThrown );
    } )
    .then( function( errorThrown, textStatus, jqXHR ) {

    } )
    .always(function() {
        // remove wait class elements
        unsetWait();
    });

});


function quizDeleteSuccess( response, textStatus, jqXHR ) {
    if(jqXHR.responseJSON === undefined) {
        // error :(
        unsetWait();
        appendMessage('Something went wrong. Please reload the page and try again.', 'error');
        return false;
    }

    response = $.parseJSON(jqXHR.responseJSON);
    displayMessages(response.message);

    var userActionAction = response.user_action.action;
    var userActionElement = response.user_action.element;
    // see if we've created a new quiz
    if(response.status === 'success' && response.action === 'update') {
        // it worked! verify that we were deleting something
        if(userActionAction === 'delete') {
            // see if it's a quiz
            if(userActionElement === 'quiz') {
                // get the quiz that was deleted
                dashItem = $('#enp-dash-item--'+response.quiz_id);
                // check if an AB Test has been deleted along with the quiz delete
                var isABTestDeleted = hasABTestDeleted(response.user_action);
                if(isABTestDeleted === true) {
                    // delete all the AB Tests
                    deleteABTestsWithQuiz(response.user_action.secondary_action.ab_test_deleted);
                }
            }
            // see if it's an AB test
            else if(userActionElement === 'ab_test') {
                dashItem = $('#enp-dash-item--'+response.ab_test_id+'a'+response.quiz_id_a+'b'+response.quiz_id_b);
            }
            // remove the dashboard item from the DOM
            removeDashItem(dashItem);

        }

    }
}
/**
* Decide which data should be sent in our AJAX request
* @param dashItem (jQuery object) of the dashboard item we're working with
* @param userAction (string) the action the user wants to do (delete-quiz, delete-ab-test)
* @return Form Data object
*/
function deleteFormData(dashItem, userAction) {
    var fd;
    if(userAction === 'delete-quiz') {
        fd = deleteQuizFormData(dashItem);
    }
    else if(userAction === 'delete-ab-test') {
        fd = deleteABTestFormData(dashItem);
    }
    return fd;
}

/**
* Generate the Form Data object for a quiz
* @param dashItem (jQuery object) of the dashboard item we're working with
* @return Form Data object
*/
function deleteQuizFormData(dashItem) {
    // get the quizID we want to delete
    var quizID = $('.enp-dash-item__quiz-id', dashItem).val();

    // get the form we're submitting
    var quizForm = document.getElementById("enp-delete-quiz-"+quizID);
    // create formData object
    var fd = new FormData(quizForm);
    // set our submit button value
    fd.append('enp-quiz-submit', 'delete-quiz');
    // append our action for wordpress AJAX call (which function it will run in class-enp_quiz-create.php)
    fd.append('action', 'save_quiz');

    return fd;
}

/**
* Generate the Form Data object for an ab test dashboard item
* @param dashItem (jQuery object) of the dashboard item we're working with
* @return Form Data object
*/
function deleteABTestFormData(dashItem) {
    // get the AB Test ID we want to delete
    var abTestID = $('.enp-dash-item__ab-test-id', dashItem).val();
    var quizIDA = $('.enp-dash-item__quiz-id-a', dashItem).val();
    var quizIDB = $('.enp-dash-item__quiz-id-b', dashItem).val();

    // get the form we're submitting
    var abTestForm = document.getElementById("enp-delete-ab-test-"+abTestID+"a"+quizIDA+"b"+quizIDB);
    // create formData object
    var fd = new FormData(abTestForm);
    // set our submit button value
    fd.append('enp-ab-test-submit', 'delete-ab-test');
    // append our action for wordpress AJAX call (which function it will run in class-enp_quiz-create.php)
    fd.append('action', 'save_ab_test');

    return fd;
}


/**
* Overlay an animation to show that we're working on deleting the dashboard item
* @param dashItem (jQuery object) of the dashboard item we're working with
*/
function deleteQuizWait(dashItem) {
    dashItem.addClass('enp-dash-item--delete-wait');
    dashItem.append(waitSpinner('enp-dash-item__spinner'));
}

/**
* Remove a dashItem from the DOM
* @param dashItem (jQuery object) of the dashboard item we're working with
*/
function removeDashItem(dashItem) {
    // remove the dashboard item animation
    dashItem.addClass('enp-dash-item--remove');

    // wait 300ms then actually remove it
    setTimeout(
        function() {
            dashItem.remove();
        },
        300
    );
}

/**
* check to see if any AB Tests also got deleted when deleting
* the quiz (because we don't want any lingering AB Tests that
* have a deleted quiz on them)
* @return (BOOLEAN) true if AB Test was also deleted, false if none
*/
function hasABTestDeleted(userActionJSON) {
    var ABTestDeleted = false;
    for(var prop in userActionJSON) {
        if(prop === 'secondary_action') {
            // loop this and see if we have an ab_test_deleted
            for(var secondary_prop in userActionJSON.secondary_action) {
                if(secondary_prop === 'ab_test_deleted') {
                    ABTestDeleted = true;
                    return ABTestDeleted;
                }
            }
        }
    }
    return ABTestDeleted;
}

/**
* If a quiz that was deleted also has an AB Test associated with
* it, then we need to delete those AB Tests too.
* This function removes all those AB Tests that were deleted from the view
* @param abTestsDeleted (JSON) from server response on which AB Tests were deleted
*/
function deleteABTestsWithQuiz(abTestsDeleted) {
    // we have AB Tests to remove. loop through them
    for (var i = 0; i < abTestsDeleted.length; i++) {
        // check to make sure it was deleted successfully
        if(abTestsDeleted[i].user_action.action === 'delete' && abTestsDeleted[i].status === 'success') {
            // get all the info we'll need to find the right AB Test to remove from the page
            var abTestID = abTestsDeleted[i].ab_test_id;
            var quizIDA = abTestsDeleted[i].quiz_id_a;
            var quizIDB = abTestsDeleted[i].quiz_id_b;
            // get the ab test dash item
            var abTestDashItem = $("#enp-dash-item--"+abTestID+"a"+quizIDA+"b"+quizIDB);
            // remove it
            removeDashItem(abTestDashItem);
        }
    }
}



/**
* Adds a waiting animation to the DOM
* @param waitClass (string) what class do you want to be added to the spinner?
*/
function waitSpinner(waitClass) {
    return '<div class="spinner '+waitClass+'"><div class="bounce bounce1"></div><div class="bounce bounce2"></div><div class="bounce bounce3"></div></div>';
}

/**
* Add wait classes to prevent duplicate submissions
* Any form submission should check to make sure we're not
* waiting on a response from the server so we don't send multiple
* submissions.
*/
function setWait() {
    // add click wait class
    $('.enp-quiz-submit').addClass('enp-quiz-submit--wait');
}

/**
* removes wait classes that prevent duplicate sumissions
*/
function unsetWait() {
    $('.enp-quiz-submit').removeClass('enp-quiz-submit--wait');
}
