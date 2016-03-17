<?/**
 * Save process for quizzes
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 *
 * Called by Enp_quiz_Quiz_create and Enp_quiz_Quiz_preview
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Save_quiz extends Enp_quiz_Save {

    public function __construct() {
        $this->pdo = new enp_quiz_Db();
    }

    public function save_quiz($quiz) {
        /* These should get generated after checking if the quiz exists */
        $quiz = $this->set_quiz_defaults($quiz);

        // Check if we should update or insert
        // If we have a row for the entry, then it exists
        $quiz_row = $this->get_quiz_row($quiz['quiz_id']);

        //  If it doesn't exist (PDO returns false if not found), then insert it. If it does exist, update it
        if($quiz_row === false) {
            // check to make sure they're not passing a high number to just guess about a quiz to update
            if($quiz['quiz_id'] === 0 ) {
                $response = $this->insert_quiz($quiz);
            } else {
                $response['errors'][] = 'Quiz ID does not exist.';
            }
        } else {
            // check to make sure that the quiz owner matches
            $allow_update = $this->quiz_owned_by_current_user($quiz_row[0]['quiz_owner'], $quiz['quiz_updated_by']);
            // update a quiz entry
            if($allow_update === true) {
                // the current user matches the quiz owner
                $response = $this->update_quiz($quiz);
            } else {
                // Hmm... the user is trying to update a quiz that isn't theirs
                $response['errors'][] = 'Quiz Update not Allowed';
            }
        }

        return $response;
    }

    /**
     * Sets the defaults for the quiz if anything needs to be set
     *
     * @param    $quiz
     * @return   returns quiz with all the defaults set
     * @since    0.0.1
     */
    public function set_quiz_defaults($quiz) {
        $quiz_defaults = array(
            'quiz_status'=> 'draft',
            'quiz_owner' => $quiz['quiz_updated_by'],
            'quiz_finish_message' => 'Thanks for taking the quiz!',
            'quiz_color_bg' => '#ffffff',
            'quiz_color_text' => '#333333',
            'quiz_color_border' => '0',
            'quiz_created_by' => $quiz['quiz_updated_by'],
            'quiz_created_at' => $quiz['quiz_updated_at'],
        );

        // merge the posted values with any defaults. Posted values will override our defaults
        $quiz = array_merge($quiz_defaults, $quiz);

        return $quiz;
    }

    /**
     * Check to see if the quiz exists or not
     *
     * @param    $quiz_id
     * @return   returns quiz row from database if exists, false if not
     * @since    0.0.1
     */
    public function get_quiz_row($quiz_id = false) {
        // check to see if the quiz
        if($quiz_id === false || $quiz_id === 0) {
            $quiz_row = false;
        } else {
            $quiz = new Enp_quiz_Quiz();
            $quiz_row = $quiz->select_quiz_by_id($quiz_id);
        }
        // return our found quiz row (or false if not found (PDO default))
        return $quiz_row;
    }

    /**
     * Check to see if the owner of the submitted quiz matches
     * the one they want to update
     *
     * @param    $quiz_owner_id = Selected quiz row from database
     * @param    $user_id = User trying to update the quiz
     * @return   returns quiz row if exists, false if not
     * @since    0.0.1
     */
    public function quiz_owned_by_current_user($quiz_owner_id = false, $current_user_id = false) {
        // set it to false to start. Guilty til proven innocent.
        $quiz_owned_by_current_user = false;

        if($quiz_owner_id !== false && $current_user_id !== false) {
            if($quiz_owner_id === $current_user_id) {
                $quiz_owned_by_current_user = true;
            }
        }

        return $quiz_owned_by_current_user;
    }

    public function insert_quiz($quiz) {
        // Get our Parameters ready
        $params = array(':quiz_title'       => $quiz['quiz_title'],
                        ':quiz_status'      => $quiz['quiz_status'],
                        ':quiz_finish_message' => $quiz['quiz_finish_message'],
                        ':quiz_color_bg'    => $quiz['quiz_color_bg'],
                        ':quiz_color_text'  => $quiz['quiz_color_text'],
                        ':quiz_color_border'=> $quiz['quiz_color_border'],
                        ':quiz_owner'       => $quiz['quiz_owner'],
                        ':quiz_created_by'  => $quiz['quiz_created_by'],
                        ':quiz_created_at'  => $quiz['quiz_created_at'],
                        ':quiz_updated_by'  => $quiz['quiz_updated_by'],
                        ':quiz_updated_at'  => $quiz['quiz_updated_at']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$this->pdo->quiz_table." (
                                            quiz_title,
                                            quiz_status,
                                            quiz_finish_message,
                                            quiz_color_bg,
                                            quiz_color_text,
                                            quiz_color_border,
                                            quiz_owner,
                                            quiz_created_by,
                                            quiz_created_at,
                                            quiz_updated_by,
                                            quiz_updated_at
                                        )
                                        VALUES(
                                            :quiz_title,
                                            :quiz_status,
                                            :quiz_finish_message,
                                            :quiz_color_bg,
                                            :quiz_color_text,
                                            :quiz_color_border,
                                            :quiz_owner,
                                            :quiz_created_by,
                                            :quiz_created_at,
                                            :quiz_updated_by,
                                            :quiz_updated_at
                                        )";
        // insert the quiz into the database
        $stmt = $this->pdo->query($sql, $params);
        $generate_response = array(
                                    'quiz_id'=> $this->pdo->lastInsertId(),
                                );

        $response = $this->generate_response($generate_response);
        return $response;
    }

    public function update_quiz($quiz) {
        $quiz = array(
            'quiz_title' => 'Wuteverz Save Updated',
            'quiz_status'=> 'draft',
        );

        $bind = array(
            ":quiz_id" => 1,
            ":quiz_owner" => $user_id
        );

        $this->pdo->query(
            // UPDATE... update query!
        );
    }

    /**
     * Save a question array in the database
     * Often used in a foreach loop to loop over all questions
     * If ID is passed, it will update that ID.
     * If no ID or ID = 0, it will insert
     *
     * @param    $question = array(); of question data
     * @return   ID of saved question or false if error
     * @since    0.0.1
     */
    public function save_question($question) {

    }

    /**
     * Save a mc_option array in the database
     * Often used in a foreach loop to loop over all mc_options
     * If ID is passed, it will update that ID.
     * If no ID or ID = 0, it will insert
     *
     * @param    $mc_option = array(); of mc_option data
     * @return   ID of saved mc_option or false if error
     * @since    0.0.1
     */
    public function save_mc_option($mc_option) {

    }

    /**
     * Save a slider array in the database
     * If ID is passed, it will update that ID.
     * If no ID or ID = 0, it will insert
     *
     * @param    $slider = array(); of slider data
     * @return   ID of saved slider or false if error
     * @since    0.0.1
     */
    public function save_slider($slider) {

    }

    /**
     * Send a respose back to the function that called it
     *
     * @param    $this->errors = an array of errors that
     * might have occurred
     * @param    Some data like the id of what was inserted, like
     *           the quiz that it's attached to
     *           the type of thing that got inserted...
     * @return   ID of saved slider or false if error
     * @since    0.0.1
     */
    public function generate_response($response = array()) {
        $response_defaults = array(
                        'errors' => array(),
        );
        $response = array_merge($response_defaults, $response);
        return $response;
    }

}
?>
