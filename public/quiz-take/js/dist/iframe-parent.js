window.addEventListener('message', receiveEnpIframeMessage, false);

// What to do when we receive a postMessage
function receiveEnpIframeMessage(event) {
    if(!/dev/.test(event.origin) && !/engagingnewsproject/.test(event.origin)) {
        return false;
    }

    console.log('data received:' + event.data);
    // make sure we got a string as our message
    if(typeof event.data !== 'string') {
        pollEnpQuizzes();
        return false;
    }

    // parse the JSON data
    data = JSON.parse(event.data);

    console.log('the data height is '+data.height);
    // set the style on the height and store to localStorage
    if(/([0-9])px/.test(data.height)) {
        // get the quiz based on ID
        var quiz = document.getElementById('enp-quiz-iframe-'+data.quiz_id);
        // set localStorage
        sessionStorage.setItem('enp-quiz-iframe-'+data.quiz_id+'-height', data.height);
        // set the height on the style
        quiz.style.height= data.height;
        console.log('height received');
    }
}

// what to do on load of an iframe
function onLoadEnpIframe() {
    // write our styles that apply to ALL quizzes
    addEnpIframeStyles();
    // call each quiz and get its height
    pollEnpQuizzes();
}

function pollEnpQuizzes() {
    // check to see if we have valid height from our PostMessage
    var quizzes = document.getElementsByClassName('enp-quiz-iframe');

    // for each quiz, check if we have a height for it. If we don't, send a
    // message to that iframe so we can get its height
    for (i = 0; i < quizzes.length; ++i) {
        // get the stored iframeheight
        quiz = quizzes[i];
        // SESSION STORAGE PERSISTS ACROSS RELOADS
        // We need to unset session storage on load
        iframeHeight = sessionStorage.getItem(quiz.id+'-height');
        console.log(iframeHeight);
        if(!/([0-9])px/.test(iframeHeight)) {
            console.log('sending request');
            // send a postMessage to get the correct height
            quiz.contentWindow.postMessage('Parent page loaded.', '*');
        } else {
            // set the height on the style
            quiz.style.height= iframeHeight;
        }
    }
}

function addEnpIframeStyles() {
    // set our styles
    var css = '.enp-quiz-iframe { -webkit-transition: all .4s ease-in-out;transition: all .4s ease-in-out; }',
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
