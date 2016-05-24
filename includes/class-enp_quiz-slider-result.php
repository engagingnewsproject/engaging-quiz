<?
/**
* Create a slider object
* @param $slider_id = the id of the slider you want to get
* @return slider object
*/
class Enp_quiz_Slider_Result extends Enp_quiz_Slider {
    public $slider_responses_total = 0,
           $slider_responses_high = 0,
           $slider_responses_low = 0,
           $slider_responses_correct = 0,
           $slider_responses_frequency = array();

    public static $results;

    public function __construct($slider_id) {
        // build the slider as a normal Enp_quiz_Slider to fill in the slider object
        parent::__construct($slider_id);
        // if it's not found, just return false
        if($slider_id === false) {
            return false;
        }
        // get the results and add them to the object
        $this->get_slider_results($slider_id);
    }

    public function get_slider_results($slider_id) {
        self::$results = $this->select_slider_question_results($slider_id);
        if(self::$results !== false) {
            self::$results = $this->set_slider_responses();
        }
        return self::$results;
    }

    public function select_slider_question_results($slider_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":slider_id" => $slider_id
        );
        $sql = "SELECT response_slider from ".$pdo->response_slider_table."
                 WHERE slider_id = :slider_id
              ORDER BY response_slider";
        $stmt = $pdo->query($sql, $params);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        // return the found results
        return $results;
    }

    /**
    * Sets entire results array.
    * Since we're grabbing just the response_slider column, we can set it directly
    *
    */
    public function set_slider_responses() {
        //$this->slider_responses = self::$results;
        $this->slider_responses_total = $this->set_slider_responses_total();
        $this->slider_responses_frequency = $this->set_slider_responses_frequency();
        // Sets responses_low, responses_correct, and responses_high
        $this->set_slider_responses_low_correct_high();
    }

    /**
    * Sets total response as integer
    */
    public function set_slider_responses_total() {
        return count(self::$results);
    }

    /**
    * Counts response frequency
    * assoc array of response by frequency array([0]=>1, [5]=>10, [10]=>9, ...)
    */
    public function set_slider_responses_frequency() {
        $response_frequency = array_count_values(self::$results);
        // create a baseline default array of all possible responses with 0 as the frequency of the response
        $default_responses = array();
        $range_low = $this->get_slider_range_low();
        $range_high = $this->get_slider_range_high();
        $increment = $this->get_slider_increment();
        $step = $range_low; // start at range low

        while($step <= $range_high) {
            $step_formatted = number_format($step, 4);
            $default_responses[$step_formatted] = 0;
            $step = $step + $increment;
        }

        // merge the arrays while preserving keys
        $response_frequency = $response_frequency + $default_responses;
        // sort by key, low to high
        ksort($response_frequency);

        return $response_frequency;
    }

    /**
    * Counts response that are low, correct, and high
    * Faster to do this in one loop instead of 3 different times
    * Sets responses_low, responses_correct, and responses_high
    */
    public function set_slider_responses_low_correct_high() {
        $correct = 0;
        $low = 0;
        $high = 0;
        $slider_correct_low = $this->get_slider_correct_low();
        $slider_correct_high = $this->get_slider_correct_high();

        if(!empty($this->slider_responses_frequency)) {
            foreach($this->slider_responses_frequency as $key=>$val) {
                // cast to float
                $key = (float) $key;
                // $key = response (ie. 10)
                // $val = frequency of response (how many times that number was chosen)
                // see if it was low
                if($key < $slider_correct_low) {
                    // it's low, so increase low by the $val (frequency of this response)
                    $low = $low + $val;
                }
                // see if the response is correct
                elseif($slider_correct_low <= $key && $key <= $slider_correct_high) {
                    // it's correct, so increase the correct number by the $val (frequency of this response)
                    $correct = $correct + $val;
                } elseif($slider_correct_high < $key) {
                    // it's high, so increase low by the $val (frequency of this response)
                    $high = $high + $val;
                }
            }
        }

        $this->slider_responses_low = $low;
        $this->slider_responses_correct = $correct;
        $this->slider_responses_high = $high;

    }

}
