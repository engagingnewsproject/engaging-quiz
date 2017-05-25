<?php
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

class Enp_quiz_Save_embed {
    public $response = array('success'=>array(),
                              'error'=>array()
                             );

    public function __construct($action, $embed_site_data) {
        // load required files
        $this->load_files();
        $embed_site_data['embed_site_updated_at'] = date("Y-m-d H:i:s");
        // start our embed save
        $save_site = new Enp_quiz_Save_embed_site();
        $this->response = $save_site->save_embed_site($action, $embed_site_data);

        header('Content-type: application/json');
        echo json_encode($this->response);
        // don't produce anymore HTML or render anything else
        // otherwise the server keeps going and sends us all
        // the HTML of the page too, but we just want the JSON data
        die();
    }

    protected function load_files() {
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
    }
}

if(isset($_POST['doing_ajax'])) {
    $save = new Enp_quiz_Save_embed($_POST['action'], $_POST);
}
