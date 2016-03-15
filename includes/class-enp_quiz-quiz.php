<?

class Enp_quiz_Quiz {
    public $id;

    public function __construct($id = false) {

    }

    public function update() {

    }

    public function insert($quiz) {
        $quiz_data = array(
        	'quiz_title' => 'Untitled',
        	'quiz_status'=> 'draft',
            'quiz_finish_message' => '',
            'quiz_color_bg' => '#ffffff',
            'quiz_color_text' => '#ffffff',
            'quiz_color_border' => 0,
            'quiz_owner' => $quiz->owner,
            'quiz_created_by' => $quiz->owner,
            'quiz_created_on'  => date("Y-m-d H:i:s"),
            'quiz_updated_by' => $quiz->owner,
            'quiz_updated_on'  => date("Y-m-d H:i:s"),
        );

        $db = new enp_quiz_Db();
        $db->insert('wp_enp_quiz', $quiz_data);

    }

    public function delete() {

    }

    public function getQuiz() {

    }

}
