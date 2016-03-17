<?/**
 * Kick off the save process. Connect to database, validation functions.
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 *
 * Extended by all the other Save classes
 *
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Save {

    public function __construct() {

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
