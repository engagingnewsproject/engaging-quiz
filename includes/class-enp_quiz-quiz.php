<?
/**
* Create a quiz object
* @param $quiz_id = the id of the quiz you want to get
* @return quiz object
*/
class Enp_quiz_Quiz {
    public  $quiz_id,
            $quiz_title,
            $quiz_status,
            $quiz_finish_message,
            $quiz_owner,
            $quiz_created_by,
            $quiz_created_at,
            $quiz_updated_by,
            $quiz_updated_at,
            $questions,
            $quiz_title_display,
            $quiz_width,
            $quiz_bg_color,
            $quiz_text_color;

    protected static $quiz;

    public function __construct($quiz_id) {
        // returns false if no quiz found
        $this->get_quiz_by_id($quiz_id);
    }

    /**
    *   Build quiz object by id
    *
    *   @param  $quiz_id = quiz_id that you want to select
    *   @return quiz object, false if not found
    **/
    public function get_quiz_by_id($quiz_id) {
        self::$quiz = $this->select_quiz_by_id($quiz_id);
        if(self::$quiz !== false) {
            self::$quiz = $this->set_quiz_object_values();
        }
        return self::$quiz;
    }

    /**
    *   For using PDO to select one quiz row
    *
    *   @param  $quiz_id = quiz_id that you want to select
    *   @return row from database table if found, false if not found
    **/
    public function select_quiz_by_id($quiz_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":quiz_id" => $quiz_id
        );
        $sql = "SELECT * from ".$pdo->quiz_table." WHERE
                quiz_id = :quiz_id
                AND quiz_is_deleted = 0";
        $stmt = $pdo->query($sql, $params);
        $quiz_row = $stmt->fetch();
        // return the found quiz row
        return $quiz_row;
    }

    /**
    * Hook up all the values for the object
    * @param $quiz = row from the quiz_table
    */
    protected function set_quiz_object_values() {
        $this->quiz_id = $this->set_quiz_id();
        $this->quiz_title = $this->set_quiz_title();
        $this->quiz_status = $this->set_quiz_status();
        $this->quiz_finish_message = $this->set_quiz_finish_message();
        $this->quiz_owner = $this->set_quiz_owner();
        $this->quiz_created_by = $this->set_quiz_created_by();
        $this->quiz_created_at = $this->set_quiz_created_at();
        $this->quiz_updated_by = $this->set_quiz_updated_by();
        $this->quiz_updated_at = $this->set_quiz_updated_at();
        //$this->quiz_color_border = $this->set_quiz_color_border();
        $this->questions = $this->set_questions();

        // set options
        $this->set_quiz_options();
    }

    /**
    * Queries the quiz options table and sets more quiz object values
    * @param $quiz_id
    */
    protected function set_quiz_options() {
        $option_rows = $this->select_quiz_options();
        foreach($option_rows as $row => $option) {
            // if it equals one of our allowed options, then set it!
            // if adding options, then add them to the allowed list here
            // or maybe create an allowed option key array?
            if($option['quiz_option_name'] === 'quiz_title_display' || 'quiz_width' || 'quiz_bg_color' || 'quiz_text_color' ) {
                // $this->quiz_title_display = value from that row
                // $this->quiz_width = value from that row
                // etc. This is just a quick setter.

                $this->$option['quiz_option_name'] = $option['quiz_option_value'];
            }
        }
    }

    /**
    *   For using PDO to select one quiz row
    *
    *   @param  $quiz_id = quiz_id that you want to select
    *   @return row from database table if found, false if not found
    **/
    protected function select_quiz_options() {
        $quiz_id = $this->quiz_id;

        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":quiz_id" => $quiz_id
        );
        $sql = "SELECT * from ".$pdo->quiz_option_table." WHERE
                quiz_id = :quiz_id";
        $stmt = $pdo->query($sql, $params);
        $option_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $option_rows;
    }

    /**
    * Set the quiz_id for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_id field from the database
    */
    protected function set_quiz_id() {
        $quiz_id = self::$quiz['quiz_id'];
        return $quiz_id;
    }

    /**
    * Set the quiz_title for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_title field from the database
    */
    protected function set_quiz_title() {
        $quiz_title = stripslashes(self::$quiz['quiz_title']);
        return $quiz_title;
    }

    /**
    * Set the quiz_status for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return 'published' or 'draft'
    */
    protected function set_quiz_status() {
        $quiz_status = self::$quiz['quiz_status'];
        if($quiz_status !== 'published') {
            $quiz_status = 'draft';
        }
        return $quiz_status;
    }

    /**
    * Set the quiz_finish_message for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_finish_message field from the database
    */
    protected function set_quiz_finish_message() {
        $quiz_finish_message = stripslashes(self::$quiz['quiz_finish_message']);
        return $quiz_finish_message;
    }

    /**
    * Set the quiz_owner for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_owner field from the database
    */
    protected function set_quiz_owner() {
        $quiz_owner = self::$quiz['quiz_owner'];
        return $quiz_owner;
    }

    /**
    * Set the created_by for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return created_by field from the database
    */
    protected function set_quiz_created_by() {
        $created_by = self::$quiz['quiz_created_by'];
        return $created_by;
    }

    /**
    * Set the created_at for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return created_at field from the database
    */
    protected function set_quiz_created_at() {
        $created_at = self::$quiz['quiz_created_at'];
        return $created_at;
    }

    /**
    * Set the updated_by for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return updated_by field from the database
    */
    protected function set_quiz_updated_by() {
        $updated_by = self::$quiz['quiz_updated_by'];
        return $updated_by;
    }

    /**
    * Set the updated_at for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return updated_at field from the database
    */
    protected function set_quiz_updated_at() {
        $updated_at = self::$quiz['quiz_updated_at'];
        return $updated_at;
    }

    /**
    * Set the quiz_bg_color for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_bg_color field from the database
    */
    /*
    protected function set_quiz_bg_color() {
        // TODO: Validate HEX
        $quiz_bg_color = self::$quiz['quiz_bg_color'];
        return $quiz_bg_color;
    }
    */

    /**
    * Set the quiz_text_color for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_text_color field from the database
    */
    /*
    protected function set_quiz_text_color() {
        // TODO: Validate HEX
        $quiz_text_color = self::$quiz['quiz_text_color'];
        return $quiz_text_color;
    }
    */
    /**
    * Set the quiz_color_border for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_color_border field from the database
    */
    /*
    protected function set_quiz_color_border() {
        // TODO: Validate HEX
        $quiz_color_border = self::$quiz['quiz_color_border'];
        return $quiz_color_border;
    }
    */
    /**
    * Set the questions for our Quiz Object
    * @param $quiz_id
    * @return questions array of ids array(3,4,5) from the database
    */
    protected function set_questions() {
        $quiz_id = self::$quiz['quiz_id'];

        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":quiz_id" => $quiz_id
        );
        $sql = "SELECT question_id from ".$pdo->question_table." WHERE
                quiz_id = :quiz_id
                AND question_is_deleted = 0
                ORDER BY question_order ASC";
        $stmt = $pdo->query($sql, $params);
        $question_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $questions = array();
        foreach($question_rows as $row => $question) {
            $questions[] = (int) $question['question_id'];
        }
        return $questions;
    }

    /**
    * Get the quiz_id for our Quiz Object
    * @param $quiz = quiz object
    * @return quiz_id from the object
    */
    public function get_quiz_id() {
        $quiz_id = $this->quiz_id;
        return $quiz_id;
    }

    /**
    * Get the quiz_title for our Quiz Object
    * @param $quiz = quiz object
    * @return quiz_title from the object
    */
    public function get_quiz_title() {
        $quiz_title = $this->quiz_title;
        return $quiz_title;
    }

    /**
    * Get the quiz_status for our Quiz Object
    * @param $quiz = quiz object
    * @return 'published' or 'draft'
    */
    public function get_quiz_status() {
        $quiz_status = $this->quiz_status;
        return $quiz_status;
    }

    /**
    * Get the quiz_finish_message for our Quiz Object
    * @param $quiz = quiz object
    * @return quiz_finish_message from the quiz object
    */
    public function get_quiz_finish_message() {
        $quiz_finish_message = $this->quiz_finish_message;
        return $quiz_finish_message;
    }

    /**
    * Get the quiz_owner for our Quiz Object
    * @param $quiz = quiz object
    * @return user_id
    */
    public function get_quiz_owner() {
        $quiz_owner = $this->quiz_owner;
        return $quiz_owner;
    }

    /**
    * Get the quiz_created_by for our Quiz Object
    * @param $quiz = quiz object
    * @return user_id
    */
    public function get_quiz_created_by() {
        $quiz_created_by = $this->quiz_created_by;
        return $quiz_created_by;
    }

    /**
    * Get the quiz_created_at for our Quiz Object
    * @param $quiz = quiz object
    * @return Date formatted Y-m-d H:i:s
    */
    public function get_quiz_created_at() {
        $quiz_created_at = $this->quiz_created_at;
        return $quiz_created_at;
    }

    /**
    * Get the quiz_updated_by for our Quiz Object
    * @param $quiz = quiz object
    * @return user_id
    */
    public function get_quiz_updated_by() {
        $quiz_updated_by = $this->quiz_updated_by;
        return $quiz_updated_by;
    }

    /**
    * Get the quiz_updated_at for our Quiz Object
    * @param $quiz = quiz object
    * @return Date formatted Y-m-d H:i:s
    */
    public function get_quiz_updated_at() {
        $quiz_updated_at = $this->quiz_updated_at;
        return $quiz_updated_at;
    }

    /**
    * Get the quiz_title_display for our Quiz Object
    * @param $quiz = quiz object
    * @return (string) 'show' or 'hide'
    */
    public function get_quiz_title_display() {
        $quiz_title_display = $this->quiz_title_display;
        return $quiz_title_display;
    }

    /**
    * Get the quiz_width for our Quiz Object
    * @param $quiz = quiz object
    * @return (string) %, px, em, or rem value (100%, 800px, 20rem, etc)
    */
    public function get_quiz_width() {
        $quiz_width = $this->quiz_width;
        return $quiz_width;
    }

    /**
    * Get the quiz_bg_color for our Quiz Object
    * @param $quiz = quiz object
    * @return #hex code
    */
    public function get_quiz_bg_color() {
        $quiz_bg_color = $this->quiz_bg_color;
        return $quiz_bg_color;
    }

    /**
    * Get the quiz_text_color for our Quiz Object
    * @param $quiz = quiz object
    * @return #hex code
    */
    public function get_quiz_text_color() {
        $quiz_text_color = $this->quiz_text_color;
        return $quiz_text_color;
    }

    /**
    * Get the quiz_color_border for our Quiz Object
    * @param $quiz = quiz object
    * @return #hex code
    */
    /*
    public function get_quiz_color_border() {
        $quiz_color_border = $this->quiz_color_border;
        return $quiz_color_border;
    }
    */
    /**
    * Get the questions for our Quiz Object
    * @param $quiz = quiz object
    * @return array of question_id's as integers
    */
    public function get_questions() {
        $questions = $this->questions;
        return $questions;
    }

    /**
    * Create an entire quiz json object with all question and mc option data
    */
    public function get_quiz_json() {
        $quiz = (array) $this;
        $question_ids = $this->get_questions();
        // create a blank question array
        // remove what we don't need
        unset($quiz['questions']);
        unset($quiz['quiz_owner']);
        unset($quiz['quiz_created_by']);
        unset($quiz['quiz_updated_by']);

        $quiz['question'] = array();
        // loop questions
        foreach($question_ids as $question_id) {
            // get question object
            $question = new Enp_quiz_Question($question_id);
            // cast object to array
            $question_array = (array) $question;
            // remove what we don't need
            unset($question_array['quiz_id']);
            unset($question_array['mc_options']);
            // get question type
            $question_type = $question->get_question_type();
            $question_array['question_image_src'] = $question->get_question_image_src();
            $question_array['question_image_srcset'] = $question->get_question_image_srcset();
            // if mc, get mc options
            if($question_type === 'mc') {
                // get the mc options
                $mc_option_ids = $question->get_mc_options();
                // create a mc_options_array
                $question_array['mc_option'] = array();
                // create the MC Options
                foreach($mc_option_ids as $mc_option_id) {
                    // build mc option object
                    $mc_option = new Enp_quiz_MC_option($mc_option_id);
                    // cast object to array in question_array
                    $question_array['mc_option'][] = (array) $mc_option;
                }
            }
            // add this question to the array we'll send via json
            $quiz['question'][] = $question_array;
        }
        return json_encode($quiz);
    }

    /**
    * Get the value we should be saving on a quiz
    * get posted if present, if not, get object. This is so we give them their
    * current entry if we don't *actually* save yet.
    * @param $string = what you want to get ('quiz_title', 'quiz_status', whatever)
    * @return $value
    */
    public function get_value($string) {
        $value = '';
        if(isset($_POST['enp_quiz'])) {
            $posted_value = $_POST['enp_quiz'];
            if(!empty($posted_value[$string])) {
                $value = stripslashes($posted_value[$string]);
            }

        }
        // if the value didn't get set, try with our object
        if($value === '') {
            $get_obj_value = 'get_'.$string;
            $obj_value = $this->$get_obj_value();
            if($obj_value !== null) {
                $value = $obj_value;
            }
        }
        // send them back whatever the value should be
        return $value;
    }


}
