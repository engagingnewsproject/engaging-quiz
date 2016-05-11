jQuery( document ).ready( function( $ ) {
    // set-up accordions for question results
    $('.enp-results-question').each(function() {
        var accordion = {header: $('.enp-results-question__header', this), content: $('.enp-results-question__content', this)};
        enp_accordion__setup(accordion);
    });

    var yScaleMaxA = _.max(ab_results_json.quiz_a_scores);
    var yScaleMaxB = _.max(ab_results_json.quiz_b_scores);
    var yScaleMinA = _.min(ab_results_json.quiz_a_scores);
    var yScaleMinB = _.min(ab_results_json.quiz_b_scores);

    // see what our real max/min should be
    if(yScaleMaxA <= yScaleMaxB) {
        yScaleMax = yScaleMaxB;
    } else {
        yScaleMax = yScaleMaxA;
    }
    // set the min
    if(yScaleMinA <= yScaleMinB) {
        yScaleMin = yScaleMinA;
    } else {
        yScaleMin = yScaleMinB;
    }

    console.log(yScaleMax);
    console.log(yScaleMin);

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

    console.log(ab_results_json.ab_results_labels);
    console.log(ab_results_json.quiz_a_scores);
    console.log(ab_results_json.quiz_b_scores);
    new Chartist.Line('.enp-quiz-score__line-chart', {
      labels: ab_results_json.ab_results_labels,
      series: [
                ab_results_json.quiz_a_scores,
                ab_results_json.quiz_b_scores
            ]
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
