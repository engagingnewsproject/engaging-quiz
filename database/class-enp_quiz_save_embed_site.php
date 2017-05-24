<?php
/**
 * Save a root site that is embedded quizzes
 *
 * @link       http://engagingnewsproject.org
 * @since      1.1.0
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */

class Enp_quiz_Save_embed_site extends Enp_quiz_Save {
    public  $embed_site = false, // object
            $response = array('success'=>array(),
                              'error'=>array()
                             );

    public function __construct() {

    }

    public function save_embed_site($action, $embed_site) {
        // get the quiz if we can
        if($embed_site !== false) {
            $this->embed_site = new Enp_quiz_Embed_site($embed_site['embed_site_id']);
        }

        // decide what we need to do
        if($action === 'insert' && $this->embed_site === false) {
            // try to insert
            $this->insert_embed_site($embed_site);
        }

        return $this->response;
    }

    protected function validate_before_insert($embed_site) {
        // try to find the site embed

    }

    /**
    * Connects to DB and inserts a new embed quiz
    * @param $embed_site (array) data we'll be saving to the embed_site table
    * @return builds and returns a response message
    */
    protected function insert_embed_site($embed_site) {
        // validate
        $is_valid = $this->validate_before_insert($embed_quiz);
        // check if there are any errors
        if($this->has_errors() === true || $is_valid === false) {
            return false;
        }
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':embed_site_id'      => $embed_site['embed_site_id'],
                        ':embed_site_name'  => $embed_site['embed_site_name'],
                        ':embed_site_url'  => $embed_site['embed_site_url'],
                        ':embed_site_created_at' => $embed_site['embed_site_updated_at'],
                        ':embed_site_updated_at' => $embed_site['embed_site_updated_at']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->embed_site_table." (
                                            embed_site_id,
                                            embed_site_name,
                                            embed_site_url,
                                            embed_site_created_at,
                                            embed_site_updated_at
                                        )
                                        VALUES(
                                            :quiz_id,
                                            :embed_site_id,
                                            :embed_site_name,
                                            :embed_site_url,
                                            :embed_site_created_at,
                                            :embed_site_updated_at
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // set-up our response array
            $return = array(
                                        'embed_site_id' => $pdo->lastInsertId(),
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );

            // merge the response arrays
            $return = array_merge($embed_site, $return);

        } else {
            // handle errors
            $return['error'] = 'Insert embed quiz failed.';
        }
        // return response
        return $return;
    }

}
