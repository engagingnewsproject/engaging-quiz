jQuery( document ).ready( function( $ ) {

    // set-up accordions for question results
    $('.enp-results-question').each(function() {
        var accordion = {header: $('.enp-results-question__header', this), content: $('.enp-results-question__content', this)};
        enp_accordion__setup(accordion);
    });

    var yScaleMax = _.max(quiz_results_json.quiz_scores);
    var yScaleMin = _.min(quiz_results_json.quiz_scores);
    // get the difference between them
    var yScaleLength = yScaleMax - yScaleMin;
    // pad by 10% of the length
    var yScalePad = Math.ceil(yScaleLength * 0.1);
    // pad the max number by 10% of the length
    yScaleMax = yScaleMax + yScalePad;
    yScaleMin = yScaleMin - yScalePad;

    if(yScaleMin < 0) {
        yScaleMin = 0;
    }
    console.log(quiz_results_json.quiz_scores);
    new Chartist.Line('.enp-quiz-score__line-chart', {
      labels: quiz_results_json.quiz_scores_labels,
      series: [quiz_results_json.quiz_scores]
    }, {
      high: yScaleMax,
      low: yScaleMin,
      fullWidth: true,
      chartPadding: {
        right: 38
      },
      lineSmooth: Chartist.Interpolation.cardinal({
          fillHoles: true,
      }),
      axisY: {
          onlyInteger: true,
      }
    });

});
