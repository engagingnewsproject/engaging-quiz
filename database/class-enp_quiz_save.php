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

    /**
    * Validation function for hex keys
    * @param $string potential hex
    * @return true if hex, false if not
    */
    public function validate_hex($string) {
        $valid_hex = false;
        // validate hex string
        $matches = null;
        preg_match('/#([a-fA-F0-9]{3}){1,2}\\b/', $string, $matches);

        if(!empty($matches)) {
            $valid_hex = true;
        }
        return $valid_hex;
    }

    /**
    * Validation function for CSS measurements
    * @param $string potential CSS measurement
    * @return true if valid, false if not
    */
    public function validate_css_measurement($string) {
        $valid_CSS = false;
        // validate hex string
        $matches = null;
        preg_match("#^(auto|0)$|^[+-]?[0-9]+.?([0-9]+)?(px|rem|em|ex|%|in|cm|mm|pt|pc|vw|vh)$#", $string, $matches);

        if(!empty($matches)) {
            $valid_CSS = true;
        }
        return $valid_CSS;
    }
}
