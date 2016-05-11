jQuery( document ).ready( function( $ ) {

    // set-up accordions for question results
    $('.enp-results-question').each(function() {
        var accordion = {header: $('.enp-results-question__header', this), content: $('.enp-results-question__content', this)};
        enp_accordion__setup(accordion);
    });
    console.log(quiz_results.quiz_scores_labels);
    console.log(quiz_results.quiz_scores);
    var lineChartData = {
        labels : quiz_results.quiz_scores_labels,
        datasets : [
            {
                label: "Quiz Scores",
                borderColor: '#58C88F',
                borderWidth: 2,
                backgroundColor: "rgba(255,255,255,0)",
                data : quiz_results.quiz_scores
            }
        ]

    };

    window.onload = function(){
    var ctx = document.getElementById("enp-quiz-scores__canvas").getContext("2d");
    window.myLine = new Chart(ctx, {
        type: 'line',
        data: lineChartData,
        options: {
            responsive: true,
        }
    });
    };
});
