<?/**
 * Save process for questions
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 *
 * Called by Enp_quiz_Quiz_create and Enp_quiz_Quiz_preview
 *
 * This class defines all code for processing and saving questions
 * Questions that get passed here will already have been sanitized
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Save_question extends Enp_quiz_Save_quiz {
    protected static $question;
    // building responses
    // parent::$response_obj->add_error('Wow. Such errors.');

    public function __construct() {

    }

    /**
    * Reformat and set values for a submitted question
    *
    * @param $question = array() in this format:
    *    $question = array(
    *            'question_id' => $question['question_id'],
    *            'question_title' =>$question['question_title'],
    *            'question_type' =>$question['question_type'],
    *            'question_explanation' =>  $question['question_explanation'],
    *            'question_order' => $question['question_order'],
    *        );
    * @return nicely formatted and value validated question array
    *         ready to get passed on to mc_option or slider validation
    */
    protected function prepare_submitted_question($question) {
        self::$question = $question;
        // set the defaults/get the submitted values
        $question_id = $this->set_question_value('question_id', 0);
        $question_title = $this->set_question_value('question_title', '');
        $question_type = $this->set_question_value('question_type', 'mc');
        $question_explanation = $this->set_question_value('question_explanation', '');
        $question_order = $question['question_order'];
        // build our new array
        $prepared_question = array(
                                'question_id' => $question_id,
                                'question_title' => $question_title,
                                'question_type' => $question_type,
                                'question_explanation' => $question_explanation,
                                'question_order' => $question_order,
                            );
        // we need to preprocess_mc_options and preprocess_slider to make sure each question has at least a slider array and mc_option array
        $prepared_question = $this->preprocess_mc_options($prepared_question);
        $prepared_question = $this->preprocess_slider($prepared_question);
        // merge the prepared question array so we don't lose our mc_option or slider values
        self::$question = array_merge(self::$question, $prepared_question);

        // check what the question type is and set the values accordingly
        if(self::$question['question_type'] === 'mc') {
            // we have a mc question, so prepare the values
            // and set it as the mc_option array
            self::$question['mc_option'] = $this->prepare_submitted_mc_options(self::$question['mc_option']);
        } elseif(self::$question['question_type'] === 'slider') {
            // TODO: Process sliders
        }

        return self::$question;
    }

    /**
    * Check for mc_option array and append it if it's missing
    * because every question needs to have a mc_option and slider row with it
    */
    protected function preprocess_mc_options($question) {
        if(!array_key_exists('mc_option', $question)) {
            $question['mc_option'] = array();
        }
        return $question;
    }

    /**
    * Check for mc_option array and append it if it's missing
    * because every question needs to have a mc_option and slider row with it
    */
    protected function preprocess_slider($question) {
        if(!array_key_exists('slider', $question)) {
            $question['slider'] = array();
        }
        return $question;
    }

    /**
    * Reformat and set values for all submitted question mc_option
    *
    * @param $mc_option = array() in this format:
    *        $mc_option = array(
    *                        array(
    *                            'mc_option_id' => $question[$i]['mc_option'][$i]['mc_option_id'],
    *                            'mc_option_content' =>$question[$i]['mc_option'][$i]['mc_option_content'],
    *                            'mc_option_correct' =>  $question[$i]['mc_option'][$i]['mc_option_correct'],
    *                            'mc_option_order' => $mc_option_i,
    *                        ),
    *                    );
    * @return nicely formatted and value validated mc_option array ready for saving
    */
    protected function prepare_submitted_mc_options($mc_options) {
        // set our counter
        $i = 0;
        // open a new array
        $prepared_mc_options = array();
        // loop through all submitted $mc_options
        foreach($mc_options as $mc_option) {
            // add in our new mc_option_order value
            $mc_option['mc_option_order'] = $i;
            // create the object
            $mc_obj = new Enp_quiz_Save_mc_option();
            // prepare the values
            $mc_option = $mc_obj->prepare_submitted_mc_option($mc_option);
            // set the nicely formatted returned $mc_option
            $prepared_mc_options[$i] = $mc_option;
            // increase our counter and do it again!
            $i++;
        }
        // Return our nicely prepared_mc_options array
        return $prepared_mc_options;
    }

    /**
    * Check to see if a value was passed in parent::$quiz['question'] array
    * If it was, set it as the value. If it wasn't, set the value
    * from parent::$quiz_obj
    *
    * @param $key = key that should be set in the quiz['question'] array.
    * @param $default = int or string of default value if nothing is found
    * @return value from either parent::$quiz['question'][$question_number][$key] or parent::$quiz_obj->get_question_$key()
    */
    protected function set_question_value($key, $default) {
        $param_value = $default;
        // see if the value is already in our submitted quiz
        if(array_key_exists($key, self::$question) && self::$question[$key] !== "") {
            $param_value = self::$question[$key];
        } else {
            // check to see if there's even a question_id to try to get
            if(array_key_exists('question_id', self::$question) &&  self::$question['question_id'] !== 0) {
                // if it's not in our submited quiz, try to get it from the object
                // dynamically create the quiz getter function
                $question_obj = new Enp_quiz_Question(self::$question['question_id']);
                $get_obj_value = 'get_'.$key;
                // get the quiz object value
                $obj_value = $question_obj->$get_obj_value();
                // if the object value isn't null, then we have a value to set
                if($obj_value !== null) {
                    $param_value = $obj_value;
                }
            }
        }

        return $param_value;
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
        // set the question array
        self::$question = $question;
        // check to see if the id exists
        if(self::$question['question_id'] === 0) {
            // It doesn't exist yet, so insert it!
            $this->insert_question();
        } else {
            // we have a question_id, so update it!
            $this->update_question();
        }
    }

    /**
    * Connects to DB and inserts the question.
    * @param $question = formatted question array
    * @param $quiz_id = which quiz this question goes with
    * @return builds and returns a response message
    */
    protected function insert_question() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':quiz_id'          => parent::$quiz['quiz_id'],
                        ':question_title'   => self::$question['question_title'],
                        ':question_type'    => self::$question['question_type'],
                        ':question_explanation' => self::$question['question_explanation'],
                        ':question_order'   => self::$question['question_order'],
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->question_table." (
                                            quiz_id,
                                            question_title,
                                            question_type,
                                            question_explanation,
                                            question_order
                                        )
                                        VALUES(
                                            :quiz_id,
                                            :question_title,
                                            :question_type,
                                            :question_explanation,
                                            :question_order
                                        )";
        // insert the question into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            self::$question['question_id'] = $pdo->lastInsertId();
            // set-up our response array
            $question_response = array(
                                        'question_id' => self::$question['question_id'],
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );
            // pass the response array to our response object
            parent::$response_obj->set_question_response($question_response, self::$question);

            // pass the question on to save_mc_option or save_slider
            // add the question_id to the questions array
            $this->save_question_type_options();

        } else {
            parent::$response_obj->add_error('Question number '.$question['question_order'].' could not be added to the database. Try again and if it continues to not work, send us an email with details of how you got to this error.');
        }
    }

    /**
    * Connects to DB and updates the question.
    * @param $question = formatted question array
    * @param $quiz_id = which quiz this question goes with
    * @return builds and returns a response message
    */
    protected function update_question() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':question_id'      => self::$question['question_id'],
                        ':question_title'   => self::$question['question_title'],
                        ':question_type'    => self::$question['question_type'],
                        ':question_explanation' => self::$question['question_explanation'],
                        ':question_order'   => self::$question['question_order']
                    );
        // write our SQL statement
        $sql = "UPDATE ".$pdo->question_table."
                   SET  question_title = :question_title,
                        question_type = :question_type,
                        question_explanation = :question_explanation,
                        question_order = :question_order

                 WHERE  question_id = :question_id";
        // update the question in the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // set-up our response array
            $question_response = array(
                                        'question_id' => self::$question['question_id'],
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );
            // pass the response array to our response object
            parent::$response_obj->set_question_response($question_response, self::$question);

            // pass the question on to save_mc_option or save_slider
            $this->save_question_type_options();

        } else {
            parent::$response_obj->add_error('Question number '.self::$question['question_order'].' could not be updated.');
        }
    }

    /**
     * Chooses which question type we need to save, and passes it
     * to the correct function to save it
     * Not a great function name though...
     *
     * @param    $question = array of the question data
     */
    protected function save_question_type_options() {
        $question_type = self::$question['question_type'];
        // now try to save the mc_option or slider
        if($question_type === 'mc') {
            // pass the mc_option array for saving
            $this->save_mc_options(self::$question['mc_option']);
        } elseif($question_type === 'slider') {
            //TODO: create slider save
            //$this->save_slider_option(self::$question['slider']);
        } else {
            // hmm... what question type ARE you trying to save?
            parent::$response->add_error('Question type '.$question_type.' is not valid.');
        }
    }

    /**
     * Loop through a $question['mc_option'] array to save to the db
     *
     * @param    $mc_option = array(); of all question['mc_option'] data
     * @since    0.0.1
     */
    protected function save_mc_options($mc_options) {
        if(!empty($mc_options)){
            // loop through the questions and save each one
            foreach($mc_options as $mc_option) {
                // create a new object
                $mc_option_obj = new Enp_quiz_Save_mc_option();
                // pass to save_mc_options so we can decide
                // if we should insert or update the mc_option
                $mc_option_obj->save_mc_option($mc_option);
            }
        }
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

}
