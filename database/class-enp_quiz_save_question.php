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
    protected $question;
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
        $this->question = $question;
        // set the defaults/get the submitted values
        $question_id = $this->set_question_value('question_id', 0);
        $question_title = $this->set_question_value('question_title', '');
        $question_type = $this->set_question_value('question_type', '');
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
        // merge the prepared question array so we don't lose our mc_option or slider values
        $this->question = array_merge($this->question, $prepared_question);
        // return the prepared question array
        return $this->question;
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
        if(array_key_exists($key, $this->question) && $this->question[$key] !== "") {
            $param_value = $this->question[$key];
        } else {
            // check to see if there's even a question_id to try to get
            if(array_key_exists('question_id', $this->question) &&  $this->question['question_id'] !== 0) {
                // if it's not in our submited quiz, try to get it from the object
                // dynamically create the quiz getter function
                $question_obj = new Enp_quiz_Question($this->question['question_id']);
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
    * Connects to DB and inserts the question.
    * @param $question = formatted question array
    * @param $quiz_id = which quiz this question goes with
    * @return builds and returns a response message
    */
    protected function insert_question($question) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':quiz_id'          => parent::$quiz['quiz_id'],
                        ':question_title'   => $question['question_title'],
                        ':question_type'    => $question['question_type'],
                        ':question_explanation' => $question['question_explanation'],
                        ':question_order'   => $question['question_order'],
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
        // insert the quiz into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            parent::$response_obj->set_question_id($pdo->lastInsertId(), $question['question_order']);
            parent::$response_obj->set_question_status('success', $question['question_order']);
            parent::$response_obj->set_question_action('insert', $question['question_order']);
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
    protected function update_question($question) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':question_id'      => $question['question_id'],
                        ':question_title'   => $question['question_title'],
                        ':question_type'    => $question['question_type'],
                        ':question_explanation' => $question['question_explanation'],
                        ':question_order'   => $question['question_order']
                    );
        // write our SQL statement
        $sql = "UPDATE ".$pdo->question_table."
                   SET  question_title = :question_title,
                        question_type = :question_type,
                        question_explanation = :question_explanation,
                        question_order = :question_order

                 WHERE  question_id = :question_id";
        // insert the quiz into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {

            parent::$response_obj->set_question_id($question['question_id'], $question['question_order']);
            parent::$response_obj->set_question_status('success', $question['question_order']);
            parent::$response_obj->set_question_action('update', $question['question_order']);
        } else {
            parent::$response_obj->add_error('Question number '.$question['question_order'].' could not be updated.');
        }
    }


}
