<?php
/**
* Extremely bare wrapper based on
* http://codereview.stackexchange.com/questions/52414/my-simple-pdo-wrapper-class
* & http://stackoverflow.com/questions/20664450/is-a-pdo-wrapper-really-overkill
* to make opening PDO connections and preparing, binding, and executing connections
* faster.
*
**/

class enp_quiz_Db extends PDO {

    // Properties to store table names and errors
    public $quiz_table;
    public $quiz_option_table;
    public $question_table;
    public $question_mc_option_table;
    public $question_slider_table;
    public $ab_test_table;
    public $response_quiz_table;
    public $response_question_table;
    public $response_mc_table;
    public $response_slider_table;
    public $response_ab_test_table;
    public $embed_site_table;
    public $embed_site_type_table;
    public $embed_site_br_site_type_table;
    public $embed_quiz_table;
    public $errors;
    
    public function __construct()
    {
        // check if a connection already exists
        try {
            // Include the configuration file with connection info and variables
            include($_SERVER["DOCUMENT_ROOT"] . '/enp-quiz-database-config.php');
            // Set table names for dynamic reference
            $this->quiz_table = $enp_quiz_table_quiz;
            $this->quiz_option_table = $enp_quiz_table_quiz_option;
            $this->question_table = $enp_quiz_table_question;
            $this->question_mc_option_table = $enp_quiz_table_question_mc_option;
            $this->question_slider_table = $enp_quiz_table_question_slider;
            $this->ab_test_table = $enp_quiz_table_ab_test;
            $this->response_quiz_table = $enp_quiz_table_response_quiz;
            $this->response_question_table = $enp_quiz_table_response_question;
            $this->response_mc_table = $enp_quiz_table_response_mc;
            $this->response_slider_table = $enp_quiz_table_response_slider;
            $this->response_ab_test_table = $enp_quiz_table_ab_test_response;
            $this->embed_site_table = $enp_quiz_table_embed_site;
            $this->embed_site_type_table = $enp_quiz_table_embed_site_type;
            $this->embed_site_br_site_type_table = $enp_quiz_table_embed_site_br_site_type;
            $this->embed_quiz_table = $enp_quiz_table_embed_quiz;

            // Set options for PDO connection
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // Create a new PDO connection
            parent::__construct(
                'mysql:host=' . $enp_db_host . ';dbname=' . $enp_db_name,
                // for windows users possible fix for PDO error, change 'mysql:host=' line above to:
                // 'sqlsrv:Server=' . $enp_db_host . ';Database=' . $enp_db_name,
                $enp_db_user,
                $enp_db_password,
                $options
            );
        } catch (Exception $e) {
            // Handle PDO connection error
            error_log('PDO Connection Error: ' . $e->getMessage());
            $this->errors = $e->getMessage();
        }
    }

     // Method to run a query
    public function runQuery($sql, $params = null)
    {
        // Prepare and execute the query
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Method to fetch a single row
    public function fetchOne($sql, $params = [])
    {
        // Run the query and fetch a single row
        $stmt = $this->runQuery($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to fetch all rows
    public function fetchAll($sql, $params = [])
    {
        // Run the query and fetch all rows
        $stmt = $this->runQuery($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Get Quizzes
     *
     */
    public function getQuizzes($where = [])
    {

        $params = $this->buildParams($where);
        $sql = "SELECT * from " . $this->quiz_table . " WHERE quiz_is_deleted = 0";

        if ($where) {
            $sql .= $this->buildWhere($params, true);
        }

        return $this->fetchAll($sql, $params);
    }

    /*
     * Get Domains
     *
     */
    public function getDomains($where = [])
    {

        $params = $this->buildParams($where);
        $sql = "SELECT DISTINCT(SUBSTRING_INDEX((SUBSTRING_INDEX((SUBSTRING_INDEX(embed_site_url, '://', -1)), '/', 1)), '.', -2)) as domain from " . $this->embed_site_table;

        if ($where) {
            $sql .= $this->buildWhere($params, true);
        }

        return $this->fetchAll($sql, $params);
    }

    /*
     * Get Sites
     *
     */
    public function getSites($where = [])
    {

        $params = $this->buildParams($where);
        $sql = "SELECT * from " . $this->embed_site_table;

        if ($where) {
            $sql .= $this->buildWhere($params, true);
        }

        return $this->fetchAll($sql, $params);
    }

    /*
     * Get Embeds
     *
     */
    public function getEmbeds($where = [])
    {

        $params = $this->buildParams($where);
        $sql = "SELECT * from " . $this->embed_quiz_table;

        if ($where) {
            $sql .= $this->buildWhere($params, true);
        }

        return $this->fetchAll($sql, $params);
    }

    // TOTALS
    public function getResponsesCorrectTotal()
    {
        $sql = "SELECT COUNT(*) from " . $this->response_question_table . " WHERE response_correct = 1";
        return (int) $this->fetchOne($sql)['COUNT(*)'];
    }

    public function getResponsesIncorrectTotal()
    {
        $sql = "SELECT COUNT(*) from " . $this->response_question_table . " WHERE response_correct = 0";
        return (int) $this->fetchOne($sql)['COUNT(*)'];
    }

    public function getMCQuestionsTotal()
    {
        $sql = "SELECT COUNT(*) from " . $this->question_table . " WHERE question_type = 'mc'";
        return (int) $this->fetchOne($sql)['COUNT(*)'];
    }

    public function getSliderQuestionsTotal()
    {
        $sql = "SELECT COUNT(*) from " . $this->question_table . " WHERE question_type = 'slider'";
        return (int) $this->fetchOne($sql)['COUNT(*)'];
    }

    public function getUniqueUsersTotal()
    {
        $sql = "SELECT COUNT(DISTINCT user_id) as users
                    FROM " . $this->response_quiz_table;

        return (int) $this->fetchOne($sql)['users'];
    }
    
    // Method to build the WHERE clause of a query
    public function buildWhere($params, $where = true)
    {
        // Initialize the WHERE clause
        $sql = '';
        if ($where === true) {
            $sql = ' WHERE ';
        }
        // Build the WHERE clause based on parameters
        if (!empty($params)) {
            $i = 1;
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    // For conditions like 'date > :date'
                    $sql .= $val['key'] . ' ' . $val['operator'] . ' ' . $val['val'];
                } else {
                    // For simple key-value conditions
                    $sql .= $key . ' = ' . $val;
                }
                // Add an AND statement if not the last condition
                if ($i !== count($params)) {
                    // not the last one, so add an AND statement
                    $where .= " AND ";
                    $i++;
                }
            }
        }
        return $sql;
    }

    /**
     * Builds out bound parameters in the array by adding a : to the beginning of the array keys
     * Method to prepare parameters for binding
     * @param $params ARRAY
     * @return ARRAY
     */
    public function buildParams($params)
    {
        // Initialize an array to store bound parameters
        $bound = [];
        
        // Copy parameters to the bound array
        foreach ($params as $key => $val) {
            $bound[$key] = $val;
        }

        return $bound;
    }
}
