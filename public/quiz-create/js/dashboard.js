
// create the list view toggle elements
$('.enp-quiz-list__view').prepend('<svg class="enp-view-toggle enp-view-toggle__grid enp-icon"><use xlink:href="#icon-grid"><title>Grid View</title></use></svg><svg class="enp-view-toggle enp-view-toggle__list enp-icon"><use xlink:href="#icon-list"><title>List View</title></use></svg>');

// add active class initially to grid view
$('.enp-view-toggle__grid').addClass('enp-view-toggle__active');

// on click, add active class/remove it from the other one
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

// create the button element to show/hide the dah item nav

$('.enp-dash-item__nav').each(function() {
    $(this).addClass('enp-dash-item__nav--collapsible')
            .attr('aria-hidden', true)
            .before('<button class="enp-dash-item__menu-action" type="button" aria-expanded="false" aria-controls="'+$(this).attr('id')+'"><svg class="enp-dash-item__menu-action__icon enp-dash-item__menu-action__icon--bottom"><use xlink:href="#icon-chevron-down" /></svg><svg class="enp-dash-item__menu-action__icon enp-dash-item__menu-action__icon--top"><use xlink:href="#icon-chevron-down" /></svg></button>');
});

// show/hide the dash item nav
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

function addActiveMenuStates(dashItem) {
    // add the new active states in
    dashItem.addClass('enp-dash-item--menu-active');
    // button to activate the menu
    $('.enp-dash-item__menu-action', dashItem).attr('aria-expanded', true);
    // menu
    $('.enp-dash-item__nav', dashItem).attr('aria-hidden', false);
}

function removeActiveMenuStates(dashItem) {
    // dash item card
    dashItem.removeClass('enp-dash-item--menu-active');
    // button to activate the menu
    $('.enp-dash-item__menu-action', dashItem).attr('aria-expanded', false);
    // menu
    $('.enp-dash-item__nav', dashItem).attr('aria-hidden', true);
}


// delete a quiz click
$('.enp-dash-item__delete').click(function(e) {
    e.preventDefault();
    // get the dash item
    var dashItem = $(this).closest('.enp-dash-item');

    // TODO This should be an "undo", not a confirm
    var confirmDelete = confirm('Are you sure you want to delete '+$('.enp-dash-item__title', dashItem).text()+'?');
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

    var fd = deleteFormData(dashItem);

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
    //console.log(jqXHR.responseJSON);
    if(jqXHR.responseJSON === undefined) {
        // error :(
        unsetWait();
        appendMessage('Something went wrong. Please reload the page and try again.', 'error');
        return false;
    }

    response = $.parseJSON(jqXHR.responseJSON);

    displayMessages(response.message);

    userActionAction = response.user_action.action;
    userActionElement = response.user_action.element;
    // see if we've created a new quiz
    if(response.status === 'success' && response.action === 'update') {
        // it worked! verify that we were deleting something
        if(userActionAction === 'delete') {
            // see if it's a quiz
            if(userActionElement === 'quiz') {
                dashItem = $('#enp-dash-item--'+response.quiz_id);
            }
            // see if it's an AB test
            else if(userActionElement === 'ab_test') {
                dashItem = $('#enp-dash-item--'+response.ab_test_id+'a'+response.quiz_id_a+'b'+response.quiz_id_b);
            }

            removeDashItem(dashItem);

        }

    }
}

function deleteFormData(dashItem) {
    var fd;
    // determine if we're deleting a quiz or an AB test
    var userAction = $('.enp-dash-item__delete', dashItem).val();
    if(userAction === 'delete-quiz') {
        fd = deleteQuizFormData(dashItem);
    }
    else if(userAction === 'delete-ab-test') {
        fd = deleteABTestFormData(dashItem);
    }
    return fd;
}

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


function deleteQuizWait(dashItem) {
    dashItem.addClass('enp-dash-item--delete-wait');
    dashItem.append(waitSpinner('enp-dash-item__spinner'));
}

function removeDashItem(dashItem) {
    // remove the dashboard item
    dashItem.addClass('enp-dash-item--remove');

    // wait 300ms then actually remove it
    setTimeout(
        function() {
            dashItem.remove();
        },
        300
    );
}


// add a close icon to the cookie message
if($('.enp-quiz-message--welcome').length) {
    $('.enp-quiz-message--welcome').append('<button class="enp-quiz-message__close" type="button"><svg class="enp-quiz-message__close__icon enp-icon"><use xlink:href="#icon-close" /></svg></button>');
}
// remove the message on click
$(document).on('click', '.enp-quiz-message__close', function() {
    $(this).closest('.enp-quiz-message--welcome').remove();
});


// Add a loading animation
function waitSpinner(waitClass) {
    return '<div class="spinner '+waitClass+'"><div class="bounce bounce1"></div><div class="bounce bounce2"></div><div class="bounce bounce3"></div></div>';
}

// add wait classes to prevent duplicate submissions
function setWait() {
    // add click wait class
    $('.enp-quiz-submit').addClass('enp-quiz-submit--wait');
}

// removes wait classes that prevent duplicate sumissions
function unsetWait() {
    $('.enp-quiz-submit').removeClass('enp-quiz-submit--wait');
}
