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
class Enp_quiz_Save_mc_option extends Enp_quiz_Save_question {
    protected static $mc_option;
    // building responses
    // parent::response['messages']['errors'][]
    // parent::response['messages']['success'][], etc

    public function __construct() {

    }

    /**
    * Reformat and set values for a submitted mc_option
    *
    * @param $mc_option = array() in this format:
    *    $mc_option = array(
    *            'mc_option_id' => $mc_option['mc_option_id'],
    *            'mc_option_content' =>$mc_option['mc_option_content'],
    *            'mc_option_correct' =>  $mc_option['mc_option_correct'],
    *            'mc_option_order' => $mc_option['mc_option_order'],
    *        );
    * @return nicely formatted and value validated mc_option array ready for saving
    */
    protected function prepare_submitted_mc_option($mc_option) {
        self::$mc_option = $mc_option;
        // set the defaults/get the submitted values
        $mc_option_id = $this->set_mc_option_value('mc_option_id', 0);
        $mc_option_content = $this->set_mc_option_value('mc_option_content', '');
        $mc_option_correct = $this->set_mc_option_value('mc_option_correct', 0);
        $mc_option_order = $mc_option['mc_option_order'];


        self::$mc_option = array(
                                'mc_option_id' => $mc_option_id,
                                'mc_option_content' => $mc_option_content,
                                'mc_option_correct' => $mc_option_correct,
                                'mc_option_order' => $mc_option_order,
                            );

        return self::$mc_option;
    }

    /**
    * Check to see if a value was passed in  parent::$quiz['question'][$question_i]['mc_option'] array
    * If it was, set it as the value. If it wasn't, set the value
    * from the $mc_option_obj we'll create
    *
    * @param $key = key that should be set in the quiz['question'] array.
    * @param $default = int or string of default value if nothing is found
    * @return value from either parent::$quiz['question'][$question_i]['mc_option'][$mc_option_i] or $mc_option_obj->get_mc_option_$key()
    */
    protected function set_mc_option_value($key, $default) {
        $param_value = $default;

        // see if the value is already in our submitted quiz
        if(array_key_exists($key, self::$mc_option) && self::$mc_option[$key] !== "") {
            $param_value = self::$mc_option[$key];
        } else {
            // check to see if there's even a mc_option_id to try to get
            if(array_key_exists('mc_option_id', self::$mc_option) &&  self::$mc_option['mc_option_id'] !== 0) {
                // if it's not in our submited quiz, try to get it from the object
                // dynamically create the quiz getter function
                $mc_option_obj = new Enp_quiz_MC_option(self::$mc_option['mc_option_id']);
                $get_obj_value = 'get_'.$key;
                // get the quiz object value
                $obj_value = $mc_option_obj->$get_obj_value();
                // if the object value isn't null, then we have a value to set
                if($obj_value !== null) {
                    $param_value = $obj_value;
                }
            }
        }

        return $param_value;
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
        self::$mc_option = $mc_option;
        // check to see if the id exists
        if(self::$mc_option['mc_option_id'] === 0) {
            // It doesn't exist yet, so insert it!
            $this->insert_mc_option();
        } else {
            // we have a mc_option_id, so update it!
            $this->update_mc_option();
        }
    }


    /**
    * Connects to DB and inserts the mc_option.
    * @param $mc_option = formatted mc_option array
    * @param $question_id = which quiz this mc_option goes with
    * @return builds and returns a response message
    */
    protected function insert_mc_option() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':question_id'      => parent::$question['question_id'],
                        ':mc_option_content'=> self::$mc_option['mc_option_content'],
                        ':mc_option_correct'=> self::$mc_option['mc_option_correct'],
                        ':mc_option_order'  => self::$mc_option['mc_option_order'],
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->question_mc_option_table." (
                                            question_id,
                                            mc_option_content,
                                            mc_option_correct,
                                            mc_option_order
                                        )
                                        VALUES(
                                            :question_id,
                                            :mc_option_content,
                                            :mc_option_correct,
                                            :mc_option_order
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // set-up our response array
            $mc_option_response = array(
                                        'mc_option_id' => $pdo->lastInsertId(),
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );
            // pass the response array to our response object
            parent::$response_obj->set_mc_option_response($mc_option_response, parent::$question, self::$mc_option);
        } else {
            parent::$response_obj->add_error('Question #'.$question['question_order'].' could save add Multiple Choice Option #'.self::$mc_option['mc_option_order'].'.');
        }
    }

    /**
    * Connects to DB and updates the question.
    * @param $question = formatted question array
    * @param $question_id = which quiz this question goes with
    * @return builds and returns a response message
    */
    protected function update_mc_option() {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':mc_option_id'     => self::$mc_option['mc_option_id'],
                        ':mc_option_content'=> self::$mc_option['mc_option_content'],
                        ':mc_option_correct'=> self::$mc_option['mc_option_correct'],
                        ':mc_option_order'  => self::$mc_option['mc_option_order'],
                    );
        // write our SQL statement
        $sql = "UPDATE ".$pdo->question_mc_option_table."
                   SET  mc_option_content = :mc_option_content,
                        mc_option_correct = :mc_option_correct,
                        mc_option_order = :mc_option_order

                 WHERE  mc_option_id = :mc_option_id";
        // update the mc_option in the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {

            // set-up our response array
            $mc_option_response = array(
                                        'mc_option_id' => self::$mc_option['mc_option_id'],
                                        'status'       => 'success',
                                        'action'       => 'update'
                                );
            // pass the response array to our response object
            parent::$response_obj->set_mc_option_response($mc_option_response, parent::$question, self::$mc_option);
        } else {
            // add an error that we couldn't update the mc_option
            parent::$response_obj->add_error('Question #'.parent::$question['question_order'].' could not update Multiple Choice Option #'.self::$mc_option['mc_option_order'].'. Please try again and contact support if you continue to see this error message.');
        }
    }

}
