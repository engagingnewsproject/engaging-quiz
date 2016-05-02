jQuery( document ).ready( function( $ ) {

    // set-up accordions for question results
    $('.enp-results-question').each(function() {
        var accordion = {header: $('.enp-results-question__header', this), content: $('.enp-results-question__content', this)};
        enp_accordion__setup(accordion);
    });
    /*
    var lineChartData = {
        labels : ["0%","10%","20%","30%","40%","50%","60%", "70%", "80%", "90%", "100%"],
        datasets : [
            {
                label: "Quiz Scores",
                borderColor: '#58C88F',
                borderWidth: 2,
                backgroundColor: "rgba(255,255,255,0)",
                data : [0,0,0,2,4,10,27,42,32,17,2]
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
    };*/
});
