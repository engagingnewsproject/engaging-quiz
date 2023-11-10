<?php
// rewrite-rules.php

// Add custom rewrite rules
function custom_rewrite_rules() {
    add_rewrite_rule('^quiz-embed/([0-9]+)?$', '/wp-content/plugins/enp-quiz/public/quiz-take/templates/quiz.php?quiz_id=$1', 'top');
    add_rewrite_rule('^ab-embed/([0-9]+)?$', '/wp-content/plugins/enp-quiz/public/quiz-take/templates/ab-test.php?ab_test_id=$1', 'top');
}
add_action('init', 'custom_rewrite_rules');
