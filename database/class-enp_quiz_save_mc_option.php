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
    protected $mc_option;
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
        $this->mc_option = $mc_option;
        // set the defaults/get the submitted values
        $mc_option_id = $this->set_mc_option_value('mc_option_id', 0);
        $mc_option_content = $this->set_mc_option_value('mc_option_content', '');
        $mc_option_correct = $this->set_mc_option_value('mc_option_correct', 0);
        $mc_option_order = $mc_option['mc_option_order'];


        $this->mc_option = array(
                                'mc_option_id' => $mc_option_id,
                                'mc_option_content' => $mc_option_content,
                                'mc_option_correct' => $mc_option_correct,
                                'mc_option_order' => $mc_option_order,
                            );

        return $this->mc_option;
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
        if(array_key_exists($key, $this->mc_option) && $this->mc_option[$key] !== "") {
            $param_value = $this->mc_option[$key];
        } else {
            // check to see if there's even a mc_option_id to try to get
            if(array_key_exists('mc_option_id', $this->mc_option) &&  $this->mc_option['mc_option_id'] !== 0) {
                // if it's not in our submited quiz, try to get it from the object
                // dynamically create the quiz getter function
                $mc_option_obj = new Enp_quiz_MC_option($this->mc_option['mc_option_id']);
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


}
