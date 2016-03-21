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
    private static  $quiz,
                    $quiz_obj,
                    $response;

    public function __construct() {

    }

    public function save($quiz) {
        // first off, sanitize the whole submitted thang
        self::$quiz = $this->sanitize_array($quiz);
        // flatten it out to make it easier to work with
        self::$quiz = $this->flatten_quiz_array(self::$quiz);
        // create our object
        self::$quiz_obj = new Enp_quiz_Quiz(self::$quiz['quiz_id']);
        // fill the quiz with all the values
        self::$quiz = $this->prepare_submitted_quiz(self::$quiz);
        // actually save the quiz, and return the response
        return $this->save_quiz();
    }

    /**
    * The quiz they submit isn't in the exact format we want it in
    * so we need to reindex it to a nice format and check their values
    *
    * @param $quiz = array() in this format:
    *        $quiz = array(
    *            'quiz' => $_POST['enp_quiz'], // array of quiz values (quiz_id, quiz_title, etc)
    *            'questions' => $_POST['enp_question'], // array of questions
    *            'quiz_updated_by' => $user_id,
    *            'quiz_updated_at' => $date_time,
    *        );
    * @return nicely formatted and value validated quiz array ready for saving
    */
    protected function prepare_submitted_quiz($quiz) {

        $quiz_id = $this->set_quiz_value('quiz_id', 0);
        $quiz_title = $this->set_quiz_value('quiz_title', 'Untitled');
        $quiz_status = $this->set_quiz_value('quiz_status', 'draft');
        $quiz_finish_message = $this->set_quiz_value('quiz_finish_message', 'Thanks for taking our quiz!');
        $quiz_color_bg    = $this->set_quiz_value('quiz_color_bg', '#ffffff');
        $quiz_color_text  = $this->set_quiz_value('quiz_color_text', '#333333');
        $quiz_color_border = $this->set_quiz_value('quiz_color_border', "0");
        $quiz_updated_by = $this->set_quiz_value('quiz_updated_by', 0);
        $quiz_updated_at = $this->set_quiz_value('quiz_updated_at', date("Y-m-d H:i:s"));
        $quiz_owner = $this->set_quiz_value('quiz_owner', $quiz_updated_by);
        $quiz_created_by = $this->set_quiz_value('quiz_created_by', $quiz_updated_by);
        $quiz_created_at = $this->set_quiz_value('quiz_created_at', $quiz_updated_at);

        $default_quiz = array(
            'quiz_id' => $quiz_id,
            'quiz_title' => $quiz_title,
            'quiz_status' => $quiz_status,
            'quiz_finish_message' => $quiz_finish_message,
            'quiz_color_bg'    => $quiz_color_bg,
            'quiz_color_text'  => $quiz_color_text,
            'quiz_color_border'=> $quiz_color_border,
            'quiz_owner'      => $quiz_owner,
            'quiz_created_by' => $quiz_created_by,
            'quiz_created_at' => $quiz_created_at,
            'quiz_updated_by' => $quiz_updated_by,
            'quiz_updated_at' => $quiz_updated_at,
        );
        // We don't want to lose anything that was in the sent quiz (like questions, etc)
        // so we'll merge them to make sure we don't lose anything
        $quiz = array_merge($quiz, $default_quiz);

        return $quiz;
    }

    /**
    * Reformats quiz array into something easier to work with
    * array('quiz' => array('quiz_id'=>1, 'quiz_title'=>'Untitled',...));
    * becomes
    * array('quiz_id'=>0, quiz_title' => 'Untitled'...)
    */
    protected function flatten_quiz_array($submitted_quiz) {
        $flattened_quiz = array();
        if(array_key_exists('quiz', $submitted_quiz) && is_array($submitted_quiz['quiz'])) {
            // Flatten the submitted arrays a bit to make it easier to understand
            $flattened_quiz = $submitted_quiz['quiz'];
            // unset (delete) the quiz
            unset($submitted_quiz['quiz']);
        }

        if(array_key_exists('question', $submitted_quiz) && is_array($submitted_quiz['question'])) {
            // get the questions
            $flattened_quiz['questions'] = $submitted_quiz['questions'];
            // unset (delete) the quiz
            unset($submitted_quiz['question']);
        }

        // merge the remainder
        $flattened_quiz = array_merge($submitted_quiz, $flattened_quiz);

        return $flattened_quiz;
    }

    /*
    * Sanitize all keys and values of an array. Loops through ALL arrays (even nested)
    */
    protected function sanitize_array($array) {
        $sanitized_array = array();
        // check to make sure it's an array
        if (!is_array($array) || !count($array)) {
    		return $sanitized_array;
    	}
        // loop through each key/value
    	foreach ($array as $key => $value) {
            // sanitize the key
            $key = sanitize_key($key);

            // if it's not an array, sanitize the value
    		if (!is_array($value) && !is_object($value)) {
    			$sanitized_array[$key] = sanitize_text_field($value);
    		}

            // if it is an array, loop through that array with the same function
    		if (is_array($value)) {
    			$sanitized_array[$key] = $this->sanitize_array($value);
    		}
    	}
        // return our new, clean, sanitized array
    	return $sanitized_array;
    }

    /**
    * Core function that hooks everything together for saving
    * Inserts or Updates the quiz in the database
    *
    * @return response array of quiz_id, action, status, and errors (if any)
    */
    private function save_quiz() {
        //  If the quiz_obj doesn't exist the quiz object will set the quiz_id as null
        if(self::$quiz_obj->get_quiz_id() === null) {
            // Congratulations, quiz! You're ready for insert!
            self::$response = $this->insert_quiz();
        } else {
            // check to make sure that the quiz owner matches
            $allow_update = $this->quiz_owned_by_current_user();
            // update a quiz entry
            if($allow_update === true) {
                // the current user matches the quiz owner
                self::$response = $this->update_quiz();
            } else {
                // Hmm... the user is trying to update a quiz that isn't theirs
                self::$response['errors'][] = 'Quiz Update not Allowed';
            }
        }

        return self::$response;
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
    protected function quiz_owned_by_current_user() {
        // cast to integers to make sure we're talkin the same talk here
        $quiz_owner_id = (int) self::$quiz_obj->get_quiz_owner();
        $current_user_id = (int) self::$quiz['quiz_updated_by'];
        // set it to false to start. Guilty til proven innocent.
        $quiz_owned_by_current_user = false;
        // check to make sure we have values for each
        if($quiz_owner_id !== false && $current_user_id !== false ) {
            // check to see if the owner and user match
            if($quiz_owner_id === $current_user_id) {
                // if they match, then it's legit
                $quiz_owned_by_current_user = true;
            }
        }

        return $quiz_owned_by_current_user;
    }

    protected function insert_quiz() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':quiz_title'       => self::$quiz['quiz_title'],
                        ':quiz_status'      => self::$quiz['quiz_status'],
                        ':quiz_finish_message' => self::$quiz['quiz_finish_message'],
                        ':quiz_color_bg'    => self::$quiz['quiz_color_bg'],
                        ':quiz_color_text'  => self::$quiz['quiz_color_text'],
                        ':quiz_color_border'=> self::$quiz['quiz_color_border'],
                        ':quiz_owner'       => self::$quiz['quiz_owner'],
                        ':quiz_created_by'  => self::$quiz['quiz_created_by'],
                        ':quiz_created_at'  => self::$quiz['quiz_created_at'],
                        ':quiz_updated_by'  => self::$quiz['quiz_updated_by'],
                        ':quiz_updated_at'  => self::$quiz['quiz_updated_at']
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
            self::$response['quiz_id'] = $pdo->lastInsertId();
            self::$response['status'] = 'success';
            self::$response['action'] = 'insert';
        } else {
            self::$response['errors'][] = 'Quiz could not be added to the database. Try again and if it continues to not work, send us an email with details of how you got to this error.';
        }

        return self::$response;
    }

    protected function update_quiz() {
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
            self::$response['quiz_id'] = self::$quiz['quiz_id'];
            self::$response['status'] = 'success';
            self::$response['action'] = 'update';
        } else {
            self::$response['errors'][] = 'Quiz could not be updated. Try again and if it continues to not work, send us an email with details of how you got to this error.';
        }

        return self::$response;
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
    protected function save_question($question) {

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
    protected function save_mc_option($mc_option) {

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
    protected function save_slider($slider) {

    }

    /**
     * Populate self::$quiz array with values
     * from self::$quiz_obj if they're not present in self::$quiz
     * We need all the values to brute update, but don't want to have to pass the
     * values in every form.
     *
     * @param    $slider = array(); of slider data
     * @return   ID of saved slider or false if error
     * @since    0.0.1
     */
    protected function set_update_quiz_params() {
        $params = array(':quiz_id'          => self::$quiz_obj->get_quiz_id(),
                        ':quiz_title'       => self::$quiz['quiz_title'],
                        ':quiz_status'      => self::$quiz['quiz_status'],
                        ':quiz_finish_message' => self::$quiz['quiz_finish_message'],
                        ':quiz_color_bg'    => self::$quiz['quiz_color_bg'],
                        ':quiz_color_text'  => self::$quiz['quiz_color_text'],
                        ':quiz_color_border'=> self::$quiz['quiz_color_border'],
                        ':quiz_owner'       => self::$quiz['quiz_owner'],
                        ':quiz_updated_by'  => self::$quiz['quiz_updated_by'],
                        ':quiz_updated_at'  => self::$quiz['quiz_updated_at']
                    );
        return $params;
    }

    /**
    * Check to see if a value was passed in self::$quiz array
    * If it was, set it as the value. If it wasn't, set the value
    * from self::$quiz_obj
    *
    * @param $key = key that should be set in the quiz array.
    * @param $default = int or string of default value if nothing is found
    * @return value from either self::$quiz[$key] or self::$quiz_obj->get_quiz_$key()
    */
    protected function set_quiz_value($key, $default) {
        $param_value = $default;
        // see if the value is already in our submitted quiz
        if(array_key_exists($key, self::$quiz) && self::$quiz[$key] !== "") {
            $param_value = self::$quiz[$key];
        } else {
            // check to see if there's even an object
            if(self::$quiz_obj->get_quiz_id() !== null) {
                // if it's not in our submited quiz, try to get it from the object
                // dynamically create the quiz getter function
                $get_obj_value = 'get_'.$key;
                // get the quiz object value
                $obj_value = self::$quiz_obj->$get_obj_value();

                if($obj_value !== null) {
                    $param_value = $obj_value;
                }
            }

        }

        return $param_value;
    }

}
?>
