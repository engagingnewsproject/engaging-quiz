// turn on mustache/handlebars style templating
_.templateSettings = {
  interpolate: /\{\{(.+?)\}\}/g
};
// Templates
var mcOptionTemplate = _.template($('#mc_option_template').html());
var questionTemplate = _.template($('#question_template').html());
var questionImageTemplate = _.template($('#question_image_template').html());
var questionExplanationTemplate = _.template($('#question_explanation_template').html());
var quizEndTemplate = _.template($('#quiz_end_template').html());
