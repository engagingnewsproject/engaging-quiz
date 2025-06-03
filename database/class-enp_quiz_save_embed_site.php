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
    public $response = array(
                             'error'=>array()
                             );

    public function __construct() {
        // call $this->save_embed_site($action, $embed_site) to save
    }

    /**
    * @return $response (ARRAY) 'embed_site_id'. If it doesn't exist, it insterts it. If it does exist, it returns the existing embed_site_id.
    */
    public function save_embed_site($embed_site) {
        // sanitize it
        $embed_site = $this->sanitize_embed_site($embed_site);
        // Most likely, this site already exists. Check it and return the ID if it exists.
        $embed_site_obj = new Enp_quiz_Embed_site($embed_site['embed_site_url']);
        $embed_site_id = $embed_site_obj->get_embed_site_id();
        if($this->is_id($embed_site_id)) {
            $this->response['embed_site_id'] = $embed_site_id;
            $this->response['status'] = 'success';
            $this->response['action'] = 'found embed site id';
            return $this->response;
        }


        // decide what we need to do
        if($embed_site['action'] === 'insert') {
            // try to insert
            $this->insert_embed_site($embed_site);
        } else {
            $this->add_error('Invalid action.');
        }

        return $this->response;
    }

    protected function sanitize_embed_site($embed_site) {

        if(isset($embed_site['embed_site_name'])) {
            $embed_site['embed_site_name'] = filter_var($embed_site['embed_site_name'], FILTER_SANITIZE_STRING);
        }

        if(isset($embed_site['embed_site_url'])) {
            // get the host www.whatever.com from the url
            $embed_site['embed_site_url'] = 'http://'.parse_url($embed_site['embed_site_url'], PHP_URL_HOST);
        }

        return $embed_site;
    }

    /**
    * Validation to make sure we're allowed to insert. Checks on
    * and adds items to $this->response[errors]
    * array if any validations fails.
    *
    * @param $embed_site (ARRAY)
    *        array(embed_site_url, embed_site_updated_at)
    * @
    */
    protected function validate_before_insert($embed_site) {
        $required = array(
                        'embed_site_url',
                        'embed_site_name',
                        'embed_site_updated_at'
                    );
        foreach($required as $require) {
            if(!array_key_exists($require, $embed_site)) {
                $this->add_error($require.' is not set in the array.');
            } else if(empty($embed_site[$require])) {
                $this->add_error($require.' is empty.');
            }
        }
		
		if (!$this->is_safe_domain($embed_site['embed_site_url'])) {
			$this->add_error('Domain flagged as unsafe by Google Safe Browsing: ' . $embed_site['embed_site_url']);
			return $this->response;
		}

        // if we already have errors, then return early
        if($this->has_errors($this->response) === true) {
            return false;
        }

        $url = $embed_site['embed_site_url'];
        $site_name = $embed_site['embed_site_name'];
        $date = $embed_site['embed_site_updated_at'];

        // check that we have a valid url
        if($this->is_valid_url($url) === false) {
            $this->add_error('Invalid URL: '.$url);
        }

        if(empty($site_name)) {
            $this->add_error('No site name');
        }

        if(!is_string($site_name)) {
            $this->add_error('Invalid site name');
        }

        // check if the date is valid
        if($this->is_date($date) === false) {
            $this->add_error('Invalid date.');
        }

        // try to find the site embed
        return $this->is_valid($this->response);
    }

    /**
    * Connects to DB and inserts a new embed quiz
    * @param $embed_site (array) data we'll be saving to the embed_site table
    * @return builds and returns a response message
    */
    protected function insert_embed_site($embed_site) {
        // validate
        $valid = $this->validate_before_insert($embed_site);
        // check if there are any errors
        if($valid !== true) {
            return $this->response;
        }

        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':embed_site_name'  => $embed_site['embed_site_name'],
                        ':embed_site_url'  => $embed_site['embed_site_url'],
                        ':embed_site_created_at' => $embed_site['embed_site_updated_at'],
                        ':embed_site_updated_at' => $embed_site['embed_site_updated_at']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->embed_site_table." (
                                            embed_site_name,
                                            embed_site_url,
                                            embed_site_created_at,
                                            embed_site_updated_at
                                        )
                                        VALUES(
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
            $success = array_merge($embed_site, $return);
            $this->response = array_merge($this->response, $success);
        } else {
            // handle errors
            $this->add_error('Insert embed quiz failed.');
        }
        // return response
        return $this->response;
    }

    protected function is_safe_domain($url) {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        // Check cache first
        $cache_key = 'enp_safe_domain_' . md5($host);
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached === 'safe';
        }

		$api_key = defined('ENP_GOOGLE_SAFE_BROWSING_API_KEY') ? ENP_GOOGLE_SAFE_BROWSING_API_KEY : '';
		if (empty($api_key)) {
			// Optionally log or handle the error here
			error_log('ENP Quiz: Google Safe Browsing API key is missing!');
			// Optionally, you could return false or handle as you see fit
			return true; // Fail open, or return false to block
		}
		$endpoint = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $api_key;
        $body = json_encode([
            'client' => [
                'clientId' => 'enp-quiz-plugin',
                'clientVersion' => '1.0'
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [
                    ['url' => 'http://' . $host],
                    ['url' => 'https://' . $host]
                ]
            ]
        ]);

        $response = wp_remote_post($endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $body,
            'timeout' => 10
        ]);

        if (is_wp_error($response)) {
            // Optionally log error
            return true; // Fail open (allow) or false (block) depending on your preference
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $is_safe = empty($data['matches']);
        // Cache result for 1 day
        set_transient($cache_key, $is_safe ? 'safe' : 'unsafe', DAY_IN_SECONDS);

        return $is_safe;
    }

}
