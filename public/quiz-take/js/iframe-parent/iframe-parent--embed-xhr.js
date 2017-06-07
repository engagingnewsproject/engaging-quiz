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
    xhr.onreadystatechange = saveEnpEmbedQuizReadyState();

    embed_quiz = {
        'save': 'embed_quiz',
        'embed_site_id': data.embed_site_id,
        'quiz_id': data.quiz_id,
        'embed_quiz_url': parentURL,
        'doing_ajax': 'true',
    };


    return xhr.send(encodeURI(enpSerialize(embed_quiz)));
    console.log(response);
    return response;
}

function saveEnpEmbedQuizReadyState() {

    console.log(xhr.status);
    if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        saveEmbedQuizComplete = true;
        response = JSON.parse(xhr.responseText);
        if(response.status === 'success') {
            console.log('success!');
            return enpHandleEmbedQuizResponse(response);
        } else {
            console.log('XHR request for saveEnpEmbedQuiz successful but returned response error: '+JSON.stringify(response));
        }

    } else if (xhr.status !== 200) {
        console.log('saveEnpEmbedQuizReadyState request failed.  Returned status of ' + xhr.status);
    }
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
