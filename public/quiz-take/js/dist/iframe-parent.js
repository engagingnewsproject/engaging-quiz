/**
* Handle postMessage to set height of iframe
* 1. When the DOM is loaded, send a request to the quiz iframes to send the iframe height
* 2. Receive the message. If the request isn't what it expected, try again.
* 3. Repeat x10(?) Until get a correct response.
*/
window.addEventListener('message', receiveEnpIframeMessage, false);
// get current url
var parentURL = window.location.href;

// What to do when we receive a postMessage
function receiveEnpIframeMessage(event) {
    var iframe,
        iframe_id,
        response;

    response = {};
    // quit the postmessage loop if it's NOT from a trusted site (engagingnewsproject.org or our dev sites)
    // If you want to see what it matches/doesn't match, go here: http://regexr.com/3dpq2
    if(/https?:\/\/(?:dev\b(?!.)|(?:(?:local|dev|test)\.)?engagingnewsproject\.org|engagingnews\.(?:staging\.)?wpengine\.com)/.test(event.origin)) {
        return false;
    }


    // make sure we got a string as our message
    if(typeof event.data !== 'string') {
        return false;
    }

    // parse the JSON data
    data = JSON.parse(event.data);

    // get the quiz or ab_test iframe based on ID
    // check if it's an ab test or not
    if(data.ab_test_id === "0") {
        iframe_id = 'enp-quiz-iframe-'+data.quiz_id;
    } else {
        iframe_id = 'enp-ab-test-iframe-'+data.ab_test_id;
    }
    iframe = document.getElementById(iframe_id);
    // find out what we need to do with it
    if(data.action === 'setHeight') {
        response.setEnpQuizHeight = setEnpQuizHeight(iframe, data.height);
    } else if(data.action === 'scrollToQuiz' || data.action === 'quizRestarted') {
         response.enpScrollToQuozResponse = enpScrollToQuiz(iframe);
    }
    else if(data.action === 'sendURL') {
        // send the url of the parent (that embedded the quiz)
        response.sendEnpParentURLResponse = sendEnpParentURL();
        // log embed
        response.saveEnpEmbedSiteResponse = saveEnpEmbedSite(event.origin, data);
    }

    // send a response sayin, yea, we got it!
    // event.source.postMessage("success!", event.origin);
    return response;
}

// what to do on load of an iframe
function onLoadEnpIframe() {
    // write our styles that apply to ALL quizzes
    addEnpIframeStyles();
    // call each quiz and get its height
    getEnpQuizHeights();
    // call each quiz and send the URL of the page its embedded on
    sendEnpParentURL();

}




function getEnpQuizHeights() {
    var quizzes,
        quiz,
        request;
    // check to see if we have valid height from our PostMessage
    quizzes = document.getElementsByClassName('enp-quiz-iframe');

    // for each quiz, send a message to that iframe so we can get its height
    for (var i = 0; i < quizzes.length; ++i) {
        // get the stored iframeheight
        quiz = quizzes[i];
        // send a postMessage to get the correct height
        request = '{"status":"request","action":"sendBodyHeight"}';
        quiz.contentWindow.postMessage(request, quiz.src);
    }
}

/**
* Send the full URL and path of the current page (the parent page)
* so it can be appended in the Share URLs
*/
function sendEnpParentURL() {
    var quizzes,
        quiz,
        request;


    // first, check to make sure we're not on the quiz preview page.
    // If we are, we shouldn't send the URL because we don't want
    // to set the quiz preview URL as the share URL
    if(/https?:\/\/(?:dev\/quiz|(?:(?:local|dev|test)\.)?engagingnewsproject\.org)\/enp-quiz\/quiz-preview\/\d+\b/.test(parentURL)) {
        // if it equals one of our site preview pages, abandon ship
        return false;
    }
    // get all the embedded quizzes
    quizzes = document.getElementsByClassName('enp-quiz-iframe');

    // for each quiz, send a message to that iframe so we can get its height
    for (var i = 0; i < quizzes.length; ++i) {
        // get the stored iframeheight
        quiz = quizzes[i];
        // send a postMessage to get the correct height
        request = '{"status":"request","action":"setShareURL","parentURL":"'+parentURL+'"}';
        quiz.contentWindow.postMessage(request, quiz.src);
    }
}





/**
* Sets the height of the iframe on the page
*/
function setEnpQuizHeight(iframe, height) {
    var response = false;
    // Test the data
    if(/([0-9])px/.test(height)) {
        // set the height on the style
        iframe.style.height= height;
        response = height;
    }
    return response;
}

/**
* Snaps the quiz to the top of the viewport, if needed
*/
function enpScrollToQuiz(iframe) {
    var response = false;
    // this will get the current quiz distance from the top of the viewport
    var distanceFromTopOfViewport = iframe.getBoundingClientRect().top;
    // see if we're within -20px and 100px of the question (negative numbers means we've scrolled PAST (down) the quiz)
    if( -20 < distanceFromTopOfViewport && distanceFromTopOfViewport < 100) {
        // Question likely within viewport. Do not scroll.
        response = 'noScroll';
    } else {
        // let's scroll them to the top of the next question (some browsers like iPhone Safari jump them way down the page)
        scrollBy(0, (distanceFromTopOfViewport - 10));
        response = 'scrolledTop';
    }
    return response;
}

function addEnpIframeStyles() {
    // set our styles
    var css = '.enp-quiz-iframe { -webkit-transition: all .25s ease-in-out;transition: all .25s ease-in-out; }',
    head = document.head || document.getElementsByTagName('head')[0],
    style = document.createElement('style');

    style.type = 'text/css';
    if (style.styleSheet){
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }

    head.appendChild(style);
}

// On Load with fallbacks
var alreadyrunflag=0; //flag to indicate whether target function has already been run

if (document.addEventListener) {
     document.addEventListener("DOMContentLoaded", function(){
        alreadyrunflag=1;
        onLoadEnpIframe();
    }, false);
}
// not so great check for IE
else if (document.all && !window.opera) {
    document.open();
    document.write('<script type="text/javascript" id="contentloadtag" defer="defer" src="javascript:void(0)"><\/script>');
    document.close();
    var contentloadtag=document.getElementById("contentloadtag");
    contentloadtag.onreadystatechange = function(){
        if (this.readyState=="complete"){
            alreadyrunflag=1;
            onLoadEnpIframe();
        }
    };
}

window.onload = function(){
    setTimeout(setTimeoutLoadCheck, 0);
};

function setTimeoutLoadCheck() {
    if (!alreadyrunflag) {
        onLoadEnpIframe();
    }
}

function enpSerialize(data) {
  var result = '';

  for(var key in data) {
      result += key + '=' + data[key] + '&';
  }
  result = result.slice(0, result.length - 1);
  return result;
}

var saveEmbedSiteComplete = false;
var saveEmbedQuizComplete = false;

function saveEnpEmbedSite(origin, data) {
    if(saveEmbedSiteComplete === true) {
        return false;
    }

    var response;
    var xhr = new XMLHttpRequest();

    xhr.open('POST', origin+'/wp-content/plugins/enp-quiz/database/class-enp_quiz_save_embed.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            saveEmbedSiteComplete = true;
            response = JSON.parse(xhr.responseText);
            if(response.status === 'success') {
                response.origin = origin;
                response.quiz_id = data.quiz_id;
                enpHandleEmbedSiteResponse(response);
            } else {
                console.log('XHR request for saveEnpEmbedSite successful but returned response error: '+JSON.stringify(response));
            }

        } else if (xhr.status !== 200) {
            console.log('Request failed.  Returned status of ' + xhr.status);
        }
    };


    data.embed_site_url = window.location.href;
    data.embed_site_name = window.location.href;
    data.save = 'embed_site';
    data.action = 'insert';
    data.doing_ajax = 'true';


    xhr.send(encodeURI(enpSerialize(data)));
}

function saveEnpEmbedQuiz(data) {
    if(saveEmbedQuizComplete === true) {
        return false;
    }

    var response;
    var xhr = new XMLHttpRequest();

    xhr.open('POST', data.origin+'/wp-content/plugins/enp-quiz/database/class-enp_quiz_save_embed.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            saveEmbedQuizComplete = true;
            response = JSON.parse(xhr.responseText);
            if(response.status === 'success') {
                enpHandleEmbedQuizResponse(response);
            } else {
                console.log('XHR request for saveEnpEmbedQuiz successful but returned response error: '+JSON.stringify(response));
            }

        } else if (xhr.status !== 200) {
            console.log('Request failed.  Returned status of ' + xhr.status);
        }
    };

    embed_quiz = {
        'save': 'embed_quiz',
        'embed_site_id': data.embed_site_id,
        'quiz_id': data.quiz_id,
        'embed_quiz_url': parentURL,
        'doing_ajax': 'true',
    };

    xhr.send(encodeURI(enpSerialize(embed_quiz)));
}

/**
* What to do after we recieve a response about saving the embed site
*/
function enpHandleEmbedSiteResponse(response) {
    var embedSiteID = response.embed_site_id;
    if(0 < parseInt(embedSiteID) ) {
        // send a request to save/update the enp_embed_quiz table
        saveEnpEmbedQuiz(response);
    } else {
        console.log('Could\'t locate a valid Embed Site');
    }

}

/**
* What to do after we recieve a response about saving the embed site
*/
function enpHandleEmbedQuizResponse(response) {
    var embedQuizID = response.embed_quiz_id;
    if(0 < parseInt(embedQuizID) ) {
        return response;
    } else {
        console.log('Could\'t locate a valid Embed Quiz');
    }

}
