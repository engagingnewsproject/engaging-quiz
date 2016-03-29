<?
/**
* Create a question object
* @param $question_id = the id of the question you want to get
* @return question object
*/
class Enp_quiz_Question {
    public  $question_id,
            $question_title,
            $question_type,
            $question_explanation,
            $question_order,
            $mc_options = array();

    protected static $question;


    public function __construct($question_id) {
        // returns false if no question found
        $this->get_question_by_id($question_id);
    }

    /**
    *   Build question object by id
    *
    *   @param  $question_id = question_id that you want to select
    *   @return question object, false if not found
    **/
    public function get_question_by_id($question_id) {
        self::$question = $this->select_question_by_id($question_id);
        if(self::$question !== false) {
            self::$question = $this->set_question_object_values();
        }
        return self::$question;
    }

    /**
    *   For using PDO to select one question row
    *
    *   @param  $question_id = question_id that you want to select
    *   @return row from database table if found, false if not found
    **/
    public function select_question_by_id($question_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":question_id" => $question_id
        );
        $sql = "SELECT * from ".$pdo->question_table." WHERE
                question_id = :question_id
                AND question_is_deleted = 0";
        $stmt = $pdo->query($sql, $params);
        $question_row = $stmt->fetch();
        // return the found question row
        return $question_row;
    }

    /**
    * Hook up all the values for the object
    * @param $question = row from the question_table
    */
    protected function set_question_object_values() {
        $this->question_id = $this->set_question_id();
        $this->question_title = $this->set_question_title();
        $this->question_image = $this->set_question_image();
        $this->question_image_alt = $this->set_question_image_alt();
        $this->question_type = $this->set_question_type();
        $this->question_explanation = $this->set_question_explanation();
        $this->question_order = $this->set_question_order();
        $this->question_is_deleted = $this->set_question_is_deleted();
        if($this->question_type === 'mc') {
            $this->mc_options = $this->set_mc_options();
        } else {
            // TODO: set_slider
        }
    }

    /**
    * Set the question_id for our Question Object
    * @param $question = question row from question database table
    * @return question_id field from the database
    */
    protected function set_question_id() {
        $question_id = self::$question['question_id'];
        return $question_id;
    }

    /**
    * Set the question_title for our Quiz Object
    * @param $question = question row from question database table
    * @return question_title field from the database
    */
    protected function set_question_title() {
        $question_title = stripslashes(self::$question['question_title']);
        return $question_title;
    }

    /**
    * Set the question_image path for our Quiz Object
    * @param $question = question row from question database table
    * @return question_image path from the database
    */
    protected function set_question_image() {
        $question_image = self::$question['question_image'];
        return $question_image;
    }

    /**
    * Set the question_image_alt for our Quiz Object
    * @param $question = question row from question database table
    * @return question_image_alt field from the database
    */
    protected function set_question_image_alt() {
        $question_image_alt = stripslashes(self::$question['question_image_alt']);
        return $question_image_alt;
    }

    /**
    * Set the question_type for our Quiz Object
    * @param $question = question row from question database table
    * @return question_type field from the database
    */
    protected function set_question_type() {
        $question_type = stripslashes(self::$question['question_type']);
        return $question_type;
    }

    /**
    * Set the question_explanation for our Quiz Object
    * @param $question = question row from question database table
    * @return question_explanation field from the database
    */
    protected function set_question_explanation() {
        $question_explanation = stripslashes(self::$question['question_explanation']);
        return $question_explanation;
    }

    /**
    * Set the question_order for our Quiz Object
    * @param $question = question row from question database table
    * @return question_order field from the database
    */
    protected function set_question_order() {
        $question_order = self::$question['question_order'];
        return $question_order;
    }

    /**
    * Set the question_is_deleted for our Quiz Object
    * @param $question = question row from question database table
    * @return question_is_deleted field from the database
    */
    protected function set_question_is_deleted() {
        $question_is_deleted = self::$question['question_is_deleted'];
        return $question_is_deleted;
    }

    /**
    * Set the mc_options for our Questions Object
    * @param $quiz_id
    * @return mc_options array of ids array(3,4,5) from the database
    */
    protected function set_mc_options() {
        $question_id = self::$question['question_id'];

        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":question_id" => $question_id
        );
        $sql = "SELECT mc_option_id from ".$pdo->question_mc_option_table." WHERE
                question_id = :question_id
                AND mc_option_is_deleted = 0";
        $stmt = $pdo->query($sql, $params);
        $mc_option_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mc_options = array();

        foreach($mc_option_rows as $row => $mc_option) {

            $mc_options[] = (int) $mc_option['mc_option_id'];
        }
        return $mc_options;
    }

    /**
    * Get the question_id for our Quiz Object
    * @param $question = question object
    * @return question_id from the object
    */
    public function get_question_id() {
        $question_id = $this->question_id;
        return $question_id;
    }

    /**
    * Get the question_title for our Quiz Object
    * @param $question = question object
    * @return question_title from the object
    */
    public function get_question_title() {
        $question_title = $this->question_title;
        return $question_title;
    }

    /**
    * Get the question_image for our Quiz Object
    * @param $question = question object
    * @return question_image from the object
    */
    public function get_question_image() {
        $question_image = $this->question_image;
        return $question_image;
    }

    /**
    * Get the question_image_alt for our Quiz Object
    * @param $question = question object
    * @return question_image_alt from the object
    */
    public function get_question_image_alt() {
        $question_image_alt = $this->question_image_alt;
        return $question_image_alt;
    }

    /**
    * Get the question_type for our Quiz Object
    * @param $question = question object
    * @return question_type from the object
    */
    public function get_question_type() {
        $question_type = $this->question_type;
        return $question_type;
    }

    /**
    * Get the question_explanation for our Quiz Object
    * @param $question = question object
    * @return question_explanation from the object
    */
    public function get_question_explanation() {
        $question_explanation = $this->question_explanation;
        return $question_explanation;
    }

    /**
    * Get the question_order for our Quiz Object
    * @param $question = question object
    * @return question_order from the object
    */
    public function get_question_order() {
        $question_order = $this->question_order;
        return $question_order;
    }

    /**
    * Get the question_is_deleted for our Quiz Object
    * @param $question = question object
    * @return question_is_deleted from the object
    */
    public function get_question_is_deleted() {
        $question_is_deleted = $this->question_is_deleted;
        return $question_is_deleted;
    }

    /**
    * Get the mc_options for our Question Object
    * @param $question = question object
    * @return array of mc_option_id's as integers
    */
    public function get_mc_options() {
        $mc_options = $this->mc_options;
        return $mc_options;
    }

    /**
    * Get the value we should be saving on a question
    * get posted if present, if not, get object. This is so we give them their
    * current entry if we don't *actually* save yet.
    * @param $string = what you want to get ('question_title', 'question_explanation', whatever)
    * @param $i = which question you're trying to get a value from
    * @return $value
    */
    public function get_value($string, $i) {
        $value = '';
        if(isset($_POST['enp_question'])) {
            $posted_value = $_POST['enp_question'];
            if(!empty($posted_value[$i][$string])) {
                $value = stripslashes($posted_value[$i][$string]);
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
?>
