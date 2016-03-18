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
    public  $quiz,
            $quiz_obj,
            $response;

    public function __construct($quiz) {
        $this->quiz = $quiz;
        $this->quiz_obj = new Enp_quiz_Quiz($quiz['quiz_id']);
    }

    public function save_quiz() {
        //  If the quiz_obj doesn't exist the quiz object will set the quiz_id as null
        if($this->quiz_obj->get_quiz_id() === null) {
            // check to make sure they're not passing a high number to just guess about a quiz to update
            if($this->quiz['quiz_id'] === 0 ) {
                // Add the defaults in
                $this->quiz = $this->set_quiz_defaults($this->quiz);
                // Congratulations, quiz! You're ready for insert!
                $this->response = $this->insert_quiz($this->quiz);
            } else {
                $this->response['errors'][] = 'Quiz ID does not exist.';
            }
        } else {
            // check to make sure that the quiz owner matches
            $allow_update = $this->quiz_owned_by_current_user($this->quiz_obj->get_quiz_owner(), $this->quiz['quiz_updated_by']);
            // update a quiz entry
            if($allow_update === true) {
                // the current user matches the quiz owner
                $this->response = $this->update_quiz();
            } else {
                // Hmm... the user is trying to update a quiz that isn't theirs
                $this->response['errors'][] = 'Quiz Update not Allowed';
            }
        }

        return $this->response;
    }

    /**
     * Sets the defaults for the quiz if anything needs to be set
     *
     * @param    $quiz
     * @return   returns quiz with all the defaults set
     * @since    0.0.1
     */
    public function set_quiz_defaults() {
        $quiz_defaults = array(
            'quiz_status'=> 'draft',
            'quiz_owner' => $this->quiz['quiz_updated_by'],
            'quiz_finish_message' => 'Thanks for taking the quiz!',
            'quiz_color_bg' => '#ffffff',
            'quiz_color_text' => '#333333',
            'quiz_color_border' => '0',
            'quiz_created_by' => $this->quiz['quiz_updated_by'],
            'quiz_created_at' => $this->quiz['quiz_updated_at'],
        );

        // merge the posted values with any defaults. Posted values will override our defaults
        $this->quiz = array_merge($quiz_defaults, $this->quiz);

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
        // cast to integers to make sure we're talkin the same talk here
        $quiz_owner_id = (int) $quiz_owner_id;
        $current_user_id = (int) $current_user_id;
        // set it to false to start. Guilty til proven innocent.
        $quiz_owned_by_current_user = false;
        // check to make sure we have values for each
        if($quiz_owner_id !== false && $current_user_id !== false) {
            // check to see if the owner and user match
            if($quiz_owner_id === $current_user_id) {
                // if they match, then it's legit
                $quiz_owned_by_current_user = true;
            }
        }

        return $quiz_owned_by_current_user;
    }

    public function insert_quiz() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':quiz_title'       => $this->quiz['quiz_title'],
                        ':quiz_status'      => $this->quiz['quiz_status'],
                        ':quiz_finish_message' => $this->quiz['quiz_finish_message'],
                        ':quiz_color_bg'    => $this->quiz['quiz_color_bg'],
                        ':quiz_color_text'  => $this->quiz['quiz_color_text'],
                        ':quiz_color_border'=> $this->quiz['quiz_color_border'],
                        ':quiz_owner'       => $this->quiz['quiz_owner'],
                        ':quiz_created_by'  => $this->quiz['quiz_created_by'],
                        ':quiz_created_at'  => $this->quiz['quiz_created_at'],
                        ':quiz_updated_by'  => $this->quiz['quiz_updated_by'],
                        ':quiz_updated_at'  => $this->quiz['quiz_updated_at']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->quiz_table." (
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
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            $this->response['quiz_id'] = $pdo->lastInsertId();
            $this->response['status'] = 'success';
            $this->response['action'] = 'insert';
        } else {
            $this->response['errors'][] = 'Quiz could not be added to the database. Try again and if it continues to not work, send us an email with details of how you got to this error.';
        }

        return $this->response;
    }

    public function update_quiz() {
        // connect to PDO
        $pdo = new enp_quiz_Db();

        $params = $this->set_update_quiz_params();

        $sql = "UPDATE ".$pdo->quiz_table."
                     SET quiz_title = :quiz_title,
                         quiz_status = :quiz_status,
                         quiz_finish_message = :quiz_finish_message,
                         quiz_color_bg = :quiz_color_bg,
                         quiz_color_text = :quiz_color_text,
                         quiz_color_border = :quiz_color_border,
                         quiz_updated_by = :quiz_updated_by,
                         quiz_updated_at = :quiz_updated_at

                   WHERE quiz_id = :quiz_id
                     AND quiz_owner = :quiz_owner
                ";

        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            $this->response['quiz_id'] = $this->quiz['quiz_id'];
            $this->response['status'] = 'success';
            $this->response['action'] = 'update';
        } else {
            $this->response['errors'][] = 'Quiz could not be updated. Try again and if it continues to not work, send us an email with details of how you got to this error.';
        }

        return $this->response;
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
     * Populate $this->quiz array with values
     * from $this->quiz_obj if they're not present in $this->quiz
     * We need all the values to brute update, but don't want to have to pass the
     * values in every form.
     *
     * @param    $slider = array(); of slider data
     * @return   ID of saved slider or false if error
     * @since    0.0.1
     */
    public function set_update_quiz_params() {
        $params = array(':quiz_id'       => $this->quiz_obj->get_quiz_id(),
                        ':quiz_title'       => $this->set_quiz_param_value('quiz_title'),
                        ':quiz_status'      => $this->set_quiz_param_value('quiz_status'),
                        ':quiz_finish_message' => $this->set_quiz_param_value('quiz_finish_message'),
                        ':quiz_color_bg'    => $this->set_quiz_param_value('quiz_color_bg'),
                        ':quiz_color_text'  => $this->set_quiz_param_value('quiz_color_text'),
                        ':quiz_color_border'=> $this->set_quiz_param_value('quiz_color_border'),
                        ':quiz_owner'       => $this->set_quiz_param_value('quiz_owner'),
                        ':quiz_updated_by'  => $this->set_quiz_param_value('quiz_updated_by'),
                        ':quiz_updated_at'  => $this->set_quiz_param_value('quiz_updated_at')
                    );
        return $params;
    }

    /**
    * Check to see if a value was passed in $this->quiz array
    * If it was, set it as the value. If it wasn't, set the value
    * from $this->quiz_obj
    *
    * @param $key = key that should be set in the quiz array.
    * @return value from either $this->quiz[$key] or $this->quiz_obj->get_quiz_$key()
    */
    public function set_quiz_param_value($key) {
        if(array_key_exists($key, $this->quiz) && !empty($this->quiz[$key])) {
            $param_value = $this->quiz[$key];
        } else {
            // dynamically create the quiz getter function
            $get_obj_value = 'get_'.$key;
            // get the quiz object value
            $param_value = $this->quiz_obj->$get_obj_value();
        }

        return $param_value;
    }

}
?>
