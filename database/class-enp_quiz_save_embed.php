<?php
// allow all sites to access this file
header('Access-Control-Allow-Origin: *');
/**
 * Save processes for posting to
 *
 * @link       http://engagingnewsproject.org
 * @since      1.1.0
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */

 /**
  * Constructor: The constructor receives the POST data and decodes it. It then determines whether to save an embedded site or an embedded quiz based on the value of $save.
  * 
  * save_embed_site: If the value of $save is 'embed_site', it checks the validity of the URL and proceeds to save the embedded site using the Enp_quiz_Save_embed_site class.
  * 
  * save_embed_quiz: 
  * If the value of $save is 'embed_quiz', it checks the validity of the quiz URL. 
  * If the quiz already exists, it updates the existing quiz; otherwise, 
  * it inserts a new one. It uses the Enp_quiz_Save_embed_quiz class for this.
  * 
  * decode: This method is used to URL decode each value in the POST data.
  * 
  * get_response: This method returns the response generated during the save process.
  * 
  * Main Code Block: The last part of the file checks if the 'save' parameter is set in the POST data. If set, it creates an instance of Enp_quiz_Save_embed and returns the response. If the request is done via AJAX, it returns the response as JSON.
  * 
  * 
  */

// set enp-quiz-config file path (eehhhh... could be better to not use relative path stuff)
require_once '../../../enp-quiz-config.php';
// which files are required for this to run?
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz.php';
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-quiz.php';
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_embed-site.php';
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_embed-quiz.php';
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_embed-site-type.php';
require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_embed-site-bridge.php';

// Database
require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_db.php';
require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save.php';
require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_embed_quiz.php';
require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_embed_site.php';

class Enp_quiz_Save_embed extends Enp_quiz_Save {
    public $date,
           $response = array(
                              'error'=>array()
                             );

    public function __construct($embed_data) {
        $this->date = date("Y-m-d H:i:s");
        $embed_data = $this->decode($embed_data);

        do_action( 'qm/debug', $embed_data );
        $save = $embed_data['save'];

        do_action( 'qm/debug', $save );
        if($save === 'embed_site') {

            // check the URL. If it doesn't start with http we don't want it
            if($this->is_valid_http_url($embed_data['embed_site_url'])) {
                $this->save_embed_site($embed_data);
            } else {
                $this->add_error('Invalid Site URL.');
            }
        } else if($save === 'embed_quiz') {
            if($this->is_valid_http_url($embed_data['embed_quiz_url'])) {
                $this->save_embed_quiz($embed_data);
            } else {
                $this->add_error('Invalid Quiz URL.');
            }
        }


    }

    protected function save_embed_site($embed_data) {
        // load required files
        $embed_data['embed_site_updated_at'] = $this->date;
        do_action( 'qm/debug', $embed_data['embed_site_updated_at'] );
        // start our embed save
        $save_site = new Enp_quiz_Save_embed_site();
        $this->response = $save_site->save_embed_site($embed_data);

        return $this->response;
    }

    protected function save_embed_quiz($embed_data) {
        // load required files

        $embed_data['embed_quiz_updated_at'] = $this->date;

        // start our embed save
        $save_quiz = new Enp_quiz_Save_embed_quiz();
        // check if it exists. If it does, save the load. If it doesn't, insert it
        $exists = $this->does_embed_quiz_exist($embed_data);

        if($exists === true) {
            $embed_quiz = new Enp_quiz_Embed_quiz($embed_data);
            // get the ID
            $embed_data['embed_quiz_id'] = $embed_quiz->get_embed_quiz_id();
            // update it
            $embed_data['action'] = 'save_load';
        } else {
            // insert it
            $embed_data['action'] = 'insert';
        }
        $this->response = $save_quiz->save_embed_quiz($embed_data);
        return $this->response;
    }

    protected function decode($array) {
        $decoded = array();
        foreach($array as $key => $val) {
            $decoded[$key] = urldecode($val);
        }
        return $decoded;
    }

    public function get_response() {
        do_action( 'qm/debug', $this->response );
        return $this->response;
    }
}


if(isset($_POST['save'])) {
    $embed_save = new Enp_quiz_Save_embed($_POST);

    $response = $embed_save->get_response();

    if(isset($_POST['doing_ajax'])) {
        header('Content-type: application/json');
        echo json_encode($response);
        // don't produce anymore HTML or render anything else
        // otherwise the server keeps going and sends us all
        // the HTML of the page too, but we just want the JSON data
        die();
    }
    return $response;
}
