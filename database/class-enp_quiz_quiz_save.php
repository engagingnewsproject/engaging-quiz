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
class Enp_quiz_Quiz_save {


    public function __construct() {

    }

    public function save_quiz($quiz) {
        /* These should get generated after checking if the quiz exists
        MOVE ALL THIS TO A SAVE CLASS*/
            $quiz_settings = array(
                'quiz_status'=> 'draft',
                'quiz_owner' => $quiz['quiz_updated_by'],
                'quiz_finish_message' => 'Thanks for taking the quiz!',
                'quiz_color_bg' => '#ffffff',
                'quiz_color_text' => '#333333',
                'quiz_color_border' => '0',
                'quiz_created_by' => $quiz['quiz_updated_by'],
                'quiz_created_at' => $quiz['quiz_updated_at'],
            );

            // merge the posted values with any defaults
            $quiz = array_merge($quiz, $quiz_settings);

            $db = new enp_quiz_Db();
            $db->insert($db->quiz_table, $quiz);

            $response = array(
                            'quiz_id' => $quiz['quiz_id'],
                            'errors' => array(),
            );

            return $response;

        /* BRUTE UPDATE INSTEAD OF INSERT IF ALREADY EXISTS
            // get the current quiz from the database

            // check to make sure the current user matches the quiz_owner

            // update the quiz entry

            // update or insert the question entry

                // update or insert mc or slider


            $quiz = array(
                'quiz_title' => 'Wuteverz Save Updated',
                'quiz_status'=> 'draft',
            );

            $bind = array(
                ":quiz_id" => 1,
                ":quiz_owner" => $user_id
            );

            $db->update($quiz_table_name, $quiz);
        */
    }

    /**
     * Process a string to get it ready for saving. Checks if isset
     * and sanitizes it.
     *
     * @return   sanitized string or default
     * @since    0.0.1
     */
    public function process_string($posted_string, $default) {
        $string = $default;
        if(isset($_POST[$posted_string])) {
            $posted_string = sanitize_text_field($_POST[$posted_string]);
            if(!empty($posted_string)) {
                $string = $posted_string;
            }
        }
        return $string;
    }

    /**
     * Process an integer to get it ready for saving. Checks if isset
     * and casts it as an integer.
     *
     * @return   sanitized integer or default
     * @since    0.0.1
     */
    public function process_int($posted_int, $default) {
        $int = $default;
        if(isset($_POST[$posted_int])) {
            $posted_int = intval($_POST[$posted_int]);
            // if the $posted_int is greater than 0,
            // then it's a potentially valid quiz_id
            if( 0 < $posted_int ) {
                $int = $posted_int;
            }
        }
        return $int;
    }

}
?>
