/**
* Handle postMessage to set height of iframe
* 1. When the DOM is loaded, send a request to the quiz iframes to send the iframe height
* 2. Receive the message. If the request isn't what it expected, try again.
* 3. Repeat x10(?) Until get a correct response.
*/
window.addEventListener('message', receiveEnpIframeMessage, false);

// What to do when we receive a postMessage
function receiveEnpIframeMessage(event) {
    var iframe,
        iframe_id;

    // quit the postmessage loop if it's from a trusted site (engagingnewsproject.org or our dev sites)
    // If you want to see what it matches/doesn't match, go here: http://regexr.com/3dpq2
    if(!/https?:\/\/(?:dev\b(?!.)|local\.quiz\b(?!.)|(?:(?:local|dev|test)\.)?engagingnewsproject\.org)/.test(event.origin)) {
        return false;
    }

    // make sure we got a string as our message
    if(typeof event.data !== 'string') {
        return false;
    }

    // parse the JSON data
    data = JSON.parse(event.data);
    console.log(data);
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
        setEnpQuizHeight(iframe, data.height);
    } else if(data.action === 'scrollToQuiz' || data.action === 'quizRestarted') {
        enpScrollToQuiz(iframe);
    }
    else if(data.action === 'sendURL') {
        // send the url of the parent (that embedded the quiz)
        sendEnpParentURL();
    }


    // send a response sayin, yea, we got it!
    // event.source.postMessage("success!", event.origin);
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
        parentURL,
        request;
    // get current url
    parentURL = window.location.href;

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
    // Test the data
    if(/([0-9])px/.test(height)) {
        // set the height on the style
        iframe.style.height= height;
    }
}

/**
* Snaps the quiz to the top of the viewport, if needed
*/
function enpScrollToQuiz(iframe) {
    // this will get the current quiz distance from the top of the viewport
    var distanceFromTopOfViewport = iframe.getBoundingClientRect().top;
    // see if we're within -20px and 100px of the question (negative numbers means we've scrolled PAST (down) the quiz)
    if( -20 < distanceFromTopOfViewport && distanceFromTopOfViewport < 100) {
        // Question likely within viewport. Do not scroll.
    } else {
        // let's scroll them to the top of the next question (some browsers like iPhone Safari jump them way down the page)
        scrollBy(0, distanceFromTopOfViewport);
    }
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

/*
 * smoothscroll polyfill - v0.3.4
 * https://iamdustan.github.io/smoothscroll
 * 2016 (c) Dustan Kasten, Jeremias Menichelli - MIT License
 */

(function(w, d, undefined) {
  'use strict';

  /*
   * aliases
   * w: window global object
   * d: document
   * undefined: undefined
   */

  // polyfill
  function polyfill() {
    // return when scrollBehavior interface is supported
    if ('scrollBehavior' in d.documentElement.style) {
      return;
    }

    /*
     * globals
     */
    var Element = w.HTMLElement || w.Element;
    var SCROLL_TIME = 468;

    /*
     * object gathering original scroll methods
     */
    var original = {
      scroll: w.scroll || w.scrollTo,
      scrollBy: w.scrollBy,
      scrollIntoView: Element.prototype.scrollIntoView
    };

    /*
     * define timing method
     */
    var now = w.performance && w.performance.now
      ? w.performance.now.bind(w.performance) : Date.now;

    /**
     * changes scroll position inside an element
     * @method scrollElement
     * @param {Number} x
     * @param {Number} y
     */
    function scrollElement(x, y) {
      this.scrollLeft = x;
      this.scrollTop = y;
    }

    /**
     * returns result of applying ease math function to a number
     * @method ease
     * @param {Number} k
     * @returns {Number}
     */
    function ease(k) {
      return 0.5 * (1 - Math.cos(Math.PI * k));
    }

    /**
     * indicates if a smooth behavior should be applied
     * @method shouldBailOut
     * @param {Number|Object} x
     * @returns {Boolean}
     */
    function shouldBailOut(x) {
      if (typeof x !== 'object'
            || x === null
            || x.behavior === undefined
            || x.behavior === 'auto'
            || x.behavior === 'instant') {
        // first arg not an object/null
        // or behavior is auto, instant or undefined
        return true;
      }

      if (typeof x === 'object'
            && x.behavior === 'smooth') {
        // first argument is an object and behavior is smooth
        return false;
      }

      // throw error when behavior is not supported
      throw new TypeError('behavior not valid');
    }

    /**
     * finds scrollable parent of an element
     * @method findScrollableParent
     * @param {Node} el
     * @returns {Node} el
     */
    function findScrollableParent(el) {
      var isBody;
      var hasScrollableSpace;
      var hasVisibleOverflow;

      do {
        el = el.parentNode;

        // set condition variables
        isBody = el === d.body;
        hasScrollableSpace =
          el.clientHeight < el.scrollHeight ||
          el.clientWidth < el.scrollWidth;
        hasVisibleOverflow =
          w.getComputedStyle(el, null).overflow === 'visible';
      } while (!isBody && !(hasScrollableSpace && !hasVisibleOverflow));

      isBody = hasScrollableSpace = hasVisibleOverflow = null;

      return el;
    }

    /**
     * self invoked function that, given a context, steps through scrolling
     * @method step
     * @param {Object} context
     */
    function step(context) {
      // call method again on next available frame
      context.frame = w.requestAnimationFrame(step.bind(w, context));

      var time = now();
      var value;
      var currentX;
      var currentY;
      var elapsed = (time - context.startTime) / SCROLL_TIME;

      // avoid elapsed times higher than one
      elapsed = elapsed > 1 ? 1 : elapsed;

      // apply easing to elapsed time
      value = ease(elapsed);

      currentX = context.startX + (context.x - context.startX) * value;
      currentY = context.startY + (context.y - context.startY) * value;

      context.method.call(context.scrollable, currentX, currentY);

      // return when end points have been reached
      if (currentX === context.x && currentY === context.y) {
        w.cancelAnimationFrame(context.frame);
        return;
      }
    }

    /**
     * scrolls window with a smooth behavior
     * @method smoothScroll
     * @param {Object|Node} el
     * @param {Number} x
     * @param {Number} y
     */
    function smoothScroll(el, x, y) {
      var scrollable;
      var startX;
      var startY;
      var method;
      var startTime = now();
      var frame;

      // define scroll context
      if (el === d.body) {
        scrollable = w;
        startX = w.scrollX || w.pageXOffset;
        startY = w.scrollY || w.pageYOffset;
        method = original.scroll;
      } else {
        scrollable = el;
        startX = el.scrollLeft;
        startY = el.scrollTop;
        method = scrollElement;
      }

      // cancel frame when a scroll event's happening
      if (frame) {
        w.cancelAnimationFrame(frame);
      }

      // scroll looping over a frame
      step({
        scrollable: scrollable,
        method: method,
        startTime: startTime,
        startX: startX,
        startY: startY,
        x: x,
        y: y,
        frame: frame
      });
    }

    /*
     * ORIGINAL METHODS OVERRIDES
     */

    // w.scroll and w.scrollTo
    w.scroll = w.scrollTo = function() {
      // avoid smooth behavior if not required
      if (shouldBailOut(arguments[0])) {
        original.scroll.call(
          w,
          arguments[0].left || arguments[0],
          arguments[0].top || arguments[1]
        );
        return;
      }

      // LET THE SMOOTHNESS BEGIN!
      smoothScroll.call(
        w,
        d.body,
        ~~arguments[0].left,
        ~~arguments[0].top
      );
    };

    // w.scrollBy
    w.scrollBy = function() {
      // avoid smooth behavior if not required
      if (shouldBailOut(arguments[0])) {
        original.scrollBy.call(
          w,
          arguments[0].left || arguments[0],
          arguments[0].top || arguments[1]
        );
        return;
      }

      // LET THE SMOOTHNESS BEGIN!
      smoothScroll.call(
        w,
        d.body,
        ~~arguments[0].left + (w.scrollX || w.pageXOffset),
        ~~arguments[0].top + (w.scrollY || w.pageYOffset)
      );
    };

    // Element.prototype.scrollIntoView
    Element.prototype.scrollIntoView = function() {
      // avoid smooth behavior if not required
      if (shouldBailOut(arguments[0])) {
        original.scrollIntoView.call(this, arguments[0] || true);
        return;
      }

      // LET THE SMOOTHNESS BEGIN!
      var scrollableParent = findScrollableParent(this);
      var parentRects = scrollableParent.getBoundingClientRect();
      var clientRects = this.getBoundingClientRect();

      if (scrollableParent !== d.body) {
        // reveal element inside parent
        smoothScroll.call(
          this,
          scrollableParent,
          scrollableParent.scrollLeft + clientRects.left - parentRects.left,
          scrollableParent.scrollTop + clientRects.top - parentRects.top
        );
        // reveal parent in viewport
        w.scrollBy({
          left: parentRects.left,
          top: parentRects.top,
          behavior: 'smooth'
        });
      } else {
        // reveal element in viewport
        w.scrollBy({
          left: clientRects.left,
          top: clientRects.top,
          behavior: 'smooth'
        });
      }
    };
  }

  if (typeof exports === 'object') {
    // commonjs
    module.exports = { polyfill: polyfill };
  } else {
    // global
    polyfill();
  }
})(window, document);
