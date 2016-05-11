jQuery( document ).ready( function( $ ) {

    // set-up accordions for question results
    $('.enp-results-question').each(function() {
        var accordion = {header: $('.enp-results-question__header', this), content: $('.enp-results-question__content', this)};
        enp_accordion__setup(accordion);
    });


    var lineChartData = {
        labels : quiz_results_json.quiz_scores_labels,
        datasets : [
            {
                label: "Quiz Scores",
                borderColor: '#58C88F',
                borderWidth: 2,
                backgroundColor: "rgba(255,255,255,0)",
                data : quiz_results_json.quiz_scores
            }
        ]
    };
    // find the lowest and highest number in the array to set as the min/max number for the line chart scale
    var yScaleMax = _.max(quiz_results_json.quiz_scores);
    var yScaleMin = _.min(quiz_results_json.quiz_scores);
    // get the difference between them
    var yScaleLength = yScaleMax - yScaleMin;
    // pad by 10% of the length
    var yScalePad = Math.ceil(yScaleLength * 0.1);
    // pad the max number by 10% of the length
    yScaleMax = yScaleMax + yScalePad;
    yScaleMin = yScaleMin - yScalePad;

    var maxTicks = 11;
    if(yScaleLength < 11) {
        maxTicks = yScaleLength + (yScalePad * 2) + 1;
    }

    // don't let the min be below 0
    if(yScaleMin < 0) {
        yScaleMin = 0;
    }

    window.onload = function(){
    var ctx = document.getElementById("enp-quiz-scores__canvas").getContext("2d");
    window.myLine = new Chart(ctx, {
        type: 'line',
        data: lineChartData,
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        min: yScaleMin,
                        max: yScaleMax,
                        tickMarkLength: 5,
                        maxTicksLimit: maxTicks
                    }
                }]
            }
        }
    });
    };
});
