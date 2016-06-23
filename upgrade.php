<?php


// we'll need to run some MYSQL when we update the social share code so that all quizzes have all the info they need
// PDO to grab ALL unique Quiz IDs
$save_quiz = new Enp_quiz_Save_quiz();

// query to get ALL quizzes
// for now,  just do one
// foreach
$quiz_id = 1;
$quiz = new Enp_quiz_Quiz($quiz_id);
$save_quiz->save($quiz);

?>
