<?

class Enp_quiz_Quiz {
    public  $quiz_title,
            $quiz_status,
            $quiz_finish_message,
            $quiz_color_bg,
            $quiz_color_text,
            $quiz_color_border,
            $quiz_owner,
            $quiz_created_by,
            $quiz_created_at,
            $quiz_updated_by,
            $quiz_updated_at;

    public function __construct($quiz_id) {
        $this->get_quiz_by_id($quiz_id);
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
                quiz_id = :quiz_id";
        $stmt = $pdo->query($sql, $params);
        $quiz_row = $stmt->fetch();
        // return the found quiz row
        return $quiz_row;
    }

    /**
    *   Build quiz object by id
    *
    *   @param  $quiz_id = quiz_id that you want to select
    *   @return quiz object, false if not found
    **/
    public function get_quiz_by_id($quiz_id) {
        $quiz = $this->select_quiz_by_id($quiz_id);
        if($quiz !== false) {
            $quiz = $this->set_quiz_object_values($quiz);
        }
        return $quiz;
    }

    public function set_quiz_object_values($quiz) {
        $this->quiz_title = $this->set_quiz_title($quiz);
        $this->quiz_status = $this->set_quiz_status($quiz);
        $this->quiz_finish_message = $this->set_quiz_finish_message($quiz);
        /* TODO: Get these set
        $this->quiz_color_bg =
        $this->quiz_color_text =
        $this->quiz_color_border =
        $this->quiz_owner =
        $this->quiz_created_by =
        $this->quiz_created_at =
        $this->quiz_updated_by =
        $this->quiz_updated_at = */
    }

    /**
    * Set the quiz_title for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_title field from the database
    */
    public function set_quiz_title($quiz) {
        $quiz_title = $quiz['quiz_title'];
        return $quiz_title;
    }

    /**
    * Set the quiz_status for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return 'published' or 'draft'
    */
    public function set_quiz_status($quiz) {
        $quiz_status = $quiz['quiz_status'];
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
    public function set_quiz_finish_message($quiz) {
        $quiz_finish_message = $quiz['quiz_finish_message'];
        return $quiz_finish_message;
    }

    /**
    * Set the quiz_color_bg for our Quiz Object
    * @param $quiz = quiz row from quiz database table
    * @return quiz_color_bg field from the database
    */
    public function set_quiz_color_bg($quiz) {
        // TODO: Validate HEX
        $quiz_color_bg = $quiz['quiz_color_bg'];
        return $quiz_color_bg;
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


}
