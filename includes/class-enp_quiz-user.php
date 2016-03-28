<?
/**
* Create a user object and gives an overview of all the
* things that user is owner of
* @param $user_id = the id of the user you want to get
* @return user object
*/
class Enp_quiz_User {
    public $user_id,
           // array of quiz_ids
           $quizzes = array(),
           // array of ab_test ids
           $ab_tests = array();
    public static $user;

    public function __construct($user_id) {
        // set our user_id
        $this->user_id = $user_id;
        $this->set_user_object_values();
    }

    /**
    * Hook up all the values for the object
    * @param $user = row from the user_table
    */
    protected function set_user_object_values() {
        $this->quizzes = set_quizzes();
        $this->ab_tests = set_ab_tests();
    }

    /**
    * Set the questions for our Quiz Object
    * @param $quiz_id
    * @return questions array of ids array(3,4,5) from the database
    */
    protected function set_quizzes() {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":user_id" => $this->user_id
        );
        $sql = "SELECT quiz_id from ".$pdo->quiz_table." WHERE
                quiz_owner = :user_id
                AND quiz_is_deleted = 0";
        $stmt = $pdo->query($sql, $params);
        $quiz_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $quizzes = array();
        foreach($quiz_rows as $row => $quiz) {
            $quizzes[] = (int) $quiz['quiz_id'];
        }
        return $quizzes;
    }

    /**
    * Set the questions for our Quiz Object
    * @param $quiz_id
    * @return questions array of ids array(3,4,5) from the database
    */
    protected function set_ab_tests() {

        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":user_id" => $this->user_id
        );
        $sql = "SELECT ab_test_id from ".$pdo->ab_test_table." WHERE
                ab_test_owner = :user_id
                AND ab_test_is_deleted = 0";
        $stmt = $pdo->query($sql, $params);
        $ab_test_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ab_tests = array();
        foreach($ab_test_rows as $row => $ab_test) {
            $ab_tests[] = (int) $ab_test['ab_test_id'];
        }
        return $ab_tests;
    }


}
