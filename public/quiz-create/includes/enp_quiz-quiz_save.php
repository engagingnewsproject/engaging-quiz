<?

if(is_user_logged_in() === false) {
    wp_redirect( home_url( '/login/' ) );
    exit();
}

// start an empty errors array. return the errors array at the end if they exist
$errors = array();


if(empty($errors)) {
    global $wpdb;
    $quiz_table_name = $wpdb->prefix . 'enp_quiz';
    $questions_table_name = $wpdb->prefix . 'enp_questions';
    $user_id = get_current_user_id();

    $db = new enp_quiz_Db();
    if(isset($_POST['insert_quiz']) && $_POST['insert_quiz'] === true) {
        // process quiz

        $quiz = array(
            'quiz_title' => 'Wuteverz Save from form',
            'quiz_status'=> 'draft',
            'quiz_owner' => $user_id,
            'quiz_created_by' => $user_id,
        );

        $db->insert($quiz_table_name, $quiz);
    } elseif(isset($_POST['update_quiz']) && $_POST['update_quiz'] === true) {
        $quiz = array(
            'quiz_title' => 'Wuteverz Save Updated',
            'quiz_status'=> 'draft',
        );

        $bind = array(
            ":quiz_id" => 1,
            ":quiz_owner" => $user_id
        );

        $db->update($quiz_table_name, $quiz);
    }
}




// get the quiz ID if one

//
// check to see if the owner is submitting the form







?>
