function receiveEnpIframeMessage(event) {
    if(!/dev/.test(event.origin) && !/engagingnewsproject/.test(event.origin)) {
        return false;
    }
    var quiz = document.getElementById('enp-quiz-iframe');
    quiz.style.height= event.data;

    sessionStorage.setItem('enp-quiz-iframe-height', event.data);
}

window.addEventListener('message', receiveEnpIframeMessage, false);

function onLoadEnpIframe() {
    // add styles to the iframe
    var quiz = document.getElementById('enp-quiz-iframe');
    quiz.style.transition = 'all .4s ease-in-out';
    // check to see if we have valid height from our PostMessage
    iframeHeight = sessionStorage.getItem('enp-quiz-iframe-height');
    if(!/([0-9])px/.test(iframeHeight)) {
        // send a postMessage to get the correct height
        quiz.contentWindow.postMessage('Parent page loaded.', '*');
    }
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
