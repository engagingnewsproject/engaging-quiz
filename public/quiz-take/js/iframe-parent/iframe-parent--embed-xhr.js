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
            response.origin = origin;
            response.quiz_id = data.quiz_id;
            enpHandleEmbedSiteResponse(response);
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
            enpHandleEmbedQuizResponse(response.success[0]);
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
        // send a request to save/update the enp_embed_quiz table
        console.log(response);
    } else {
        console.log('Could\'t locate a valid Embed Quiz');
    }

}
