<?php
/**
 * Save a quiz embedded on a site
 *
 * @link       http://engagingnewsproject.org
 * @since      1.1.0
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */

class Enp_quiz_Save_embed_quiz extends Enp_quiz_Save {
    public  $embed_quiz = false, // object
            $response = array('success'=>array(),
                              'error'=>array()
                             );

    public function __construct() {

    }

    public function save_embed_quiz($action, $embed_quiz) {
        // get the quiz if we can
        if($embed_quiz !== false && array_key_exists('embed_quiz_id', $embed_quiz)) {
            $this->embed_quiz = new Enp_quiz_Embed_quiz($embed_quiz['embed_quiz_id']);
        }

        // decide what we need to do
        if($action === 'insert' && $this->embed_quiz === false) {
            // try to insert
            $this->insert_embed_quiz($embed_quiz);
        } else if($action === 'save_load') {
            // try to save a load
            $this->update_embed_quiz_loads($embed_quiz);
        }

        return $this->response;
    }

    public function validate_before_insert($embed_quiz) {
        $url = $embed_quiz['embed_quiz_url'];
        $quiz_id = $embed_quiz['quiz_id'];
        $site_id = $embed_quiz['embed_site_id'];

        // check that we have a valid url
        if($this->is_valid_url($url) === false) {
            $this->add_error('Invalid URL');
        }

        // check that it exists
        if($this->does_embed_quiz_exist($url) === true) {
            $this->add_error('URL already exists');
        }

        // check if the quiz exists
        if($this->does_quiz_exist($quiz_id) === false) {
            $this->add_error('Quiz doesn\'t exist');
        }

        // check if the site id exists
        if($this->does_embed_site_exist($site_id) === false) {
            $this->add_error('Embed Site doesn\'t exist');
        }

    }

    public function validate_before_save_load($embed_quiz) {
        $id = $embed_quiz['embed_quiz_id'];
        // check to see if we have one
        if($this->does_embed_quiz_exist($id) === false) {
            $this->add_error('Embed Quiz doesn\'t exist. Add the embed quiz first.');
        }

    }


    /**
    * Connects to DB and inserts a new embed quiz
    * @param $embed_quiz (array) data we'll be saving to the embed_quiz table
    * @return builds and returns a response message
    */
    public function insert_embed_quiz($embed_quiz) {
        // validate
        $this->validate_before_insert($embed_quiz);
        // check if there are any errors
        if($this->has_errors() === true) {
            return $this->response;
        }

        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':quiz_id'      => $embed_quiz['quiz_id'],
                        ':embed_site_id'      => $embed_quiz['embed_site_id'],
                        ':embed_quiz_loads'  => 1,
                        ':embed_quiz_views'  => 1,
                        ':embed_quiz_created_at' => $embed_quiz['embed_quiz_updated_at'],
                        ':embed_quiz_updated_at' => $embed_quiz['embed_quiz_updated_at']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->embed_quiz_table." (
                                            quiz_id,
                                            embed_site_id,
                                            embed_quiz_loads,
                                            embed_quiz_views,
                                            embed_quiz_created_at,
                                            embed_quiz_updated_at
                                        )
                                        VALUES(
                                            :quiz_id,
                                            :embed_site_id,
                                            :embed_quiz_loads,
                                            :embed_quiz_views,
                                            :embed_quiz_created_at,
                                            :embed_quiz_updated_at
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // set-up our response array
            $response = array(
                                        'embed_quiz_id' => $pdo->lastInsertId(),
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );

            // merge the response arrays
            $this->add_success(array_merge($embed_quiz, $response));

        } else {
            // handle errors
            $this->add_error('Insert embed quiz failed.');
        }
        // return response
        return $this->response;
    }

    /**
    * Connects to DB and adds one to the number of quiz loads
    * @param $embed_quiz (array) data we'll be saving to the embed quiz table
    * @return builds and returns a response message
    */
    protected function update_embed_quiz_loads($embed_quiz) {

        $this->validate_before_save_load($embed_quiz);

        // check if there are any errors
        if($this->has_errors() === true) {
            return $this->response;
        }
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':embed_quiz_id'      => $embed_quiz['embed_quiz_id'],
                        ':embed_quiz_updated_at' => $embed_quiz['embed_quiz_updated_at']
                    );
        // write our SQL statement
        $sql = "UPDATE ".$pdo->embed_quiz_table."
                   SET  embed_quiz_loads = embed_quiz_loads + 1,
                        embed_quiz_updated_at = :embed_quiz_updated_at
                 WHERE  embed_quiz_id = :embed_quiz_id";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {

            // set-up our response array
            $response = array(
                                        'embed_quiz_id' => $embed_quiz['embed_quiz_id'],
                                        'status'       => 'success',
                                        'action'       => 'updated_quiz_embed_loads'
                                );

            // merge the response arrays
            $this->add_success(array_merge($embed_quiz, $response));

        } else {
            // handle errors
            $this->add_error('Save quiz embed loads failed.');
        }
        // return response
        return $this->response;
    }
}
