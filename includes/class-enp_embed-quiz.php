<?/**
 * Create an embed quiz object
 * Shows info on where a quiz is embedded
 *
 * @since      1.0.1
 * @package    Enp_quiz
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Embed_quiz {
    public  $embed_quiz_id,
            $quiz_id,
            $embed_site_id,
            $embed_quiz_url,
            $embed_quiz_created_at,
            $embed_quiz_updated_at,
            $embed_quiz_is_dev;

    public function __construct($id, $get_by = 'embed_quiz') {
        return $this->get_embed_quiz($id, $get_by);
    }

    /**
    *   Build embed_quiz object by url
    *
    *   @param $query STRING can be ID or URL
    *   @return embed_quiz object, false if not found
    **/
    public function get_embed_quiz($id, $get_by = 'embed_quiz') {
        $embed_quiz = false;

        if($get_by === 'embed_quiz') {
            // default, get by embed_quiz_id
            $embed_quiz = $this->select_embed_quiz_by_id($id);
        } else if($get_by === 'quiz') {
            $embed_quiz = $this->select_embed_quizzes_by_quiz_id($id);
        } else if($get_by === 'site') {
            $embed_quiz = $this->select_embed_quizzes_by_site_id($id);
        }

        if($embed_quiz !== false) {
            $embed_quiz = $this->set_embed_quiz_object_values($embed_quiz);
        }

        return $embed_quiz;
    }

    /**
    *   For using PDO to select all a site's rows
    *
    *   @param  $site_id = embed_quiz_site that you want to get quizzes on that site
    *   @return rows from database table if found, false if not found
    **/
    public function select_embed_quizzes_by_quiz_id($quiz_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":quiz_id" => $quiz_id
        );

        $sql = "SELECT * from ".$pdo->embed_quiz_table." WHERE
                quiz_id = :quiz_id";
        $stmt = $pdo->query($sql, $params);
        $embed_quiz_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return the found site row
        return $embed_quiz_row;
    }

    /**
    *   For using PDO to select one site row
    *
    *   @param  $url = url that you want to select
    *   @return row from database table if found, false if not found
    **/
    public function select_embed_quiz_by_id($embed_quiz_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":embed_quiz_id" => $embed_quiz_id
        );

        $sql = "SELECT * from ".$pdo->embed_quiz_table." WHERE
                embed_quiz_id = :embed_quiz_id";
        $stmt = $pdo->query($sql, $params);
        $embed_quiz_row = $stmt->fetch();
        // return the found site row
        return $embed_quiz_row;
    }

    protected function set_embed_quiz_object_values($rows) {
        // check if it's an array or a single row
        if(is_array($rows[0])) {
            foreach($rows as $row) {
                $this->actually_set_embed_quiz_object_values($row);
            }
        } else {
            // only one row
            $this->actually_set_embed_quiz_object_values($rows);
        }
    }

    /**
    * Sets all object variables
    */
    protected function actually_set_embed_quiz_object_values($embed_quiz) {
         $this->set_embed_quiz_id($embed_quiz['embed_quiz_id']);
         $this->set_embed_site_id($embed_quiz['embed_site_id']);
         $this->set_quiz_id($embed_quiz['quiz_id']);
         $this->set_embed_quiz_url($embed_quiz['embed_quiz_url']);
         $this->set_embed_quiz_created_at($embed_quiz['embed_quiz_created_at']);
         $this->set_embed_quiz_updated_at($embed_quiz['embed_quiz_updated_at']);
         $this->set_embed_quiz_is_dev($embed_quiz['embed_quiz_is_dev']);
    }

    protected function set_embed_quiz_id($embed_quiz_id) {
        $this->embed_quiz_id = $embed_quiz_id;
        return $this->embed_quiz_id;
    }

    protected function set_embed_site_id($embed_site_id) {
        $this->embed_site_id = $embed_site_id;
        return $this->embed_site_id;
    }

    protected function set_quiz_id($quiz_id) {
        $this->quiz_id = $quiz_id;
        return $this->quiz_id;
    }

    protected function set_embed_quiz_url($embed_quiz_url) {
        $this->embed_quiz_url = $embed_quiz_url;
        return $this->embed_quiz_url;
    }

    protected function set_embed_quiz_created_at($embed_quiz_created_at) {
        $this->embed_quiz_created_at = $embed_quiz_created_at;
        return $this->embed_quiz_created_at;
    }

    protected function set_embed_quiz_updated_at($embed_quiz_updated_at) {
        $this->embed_quiz_updated_at = $embed_quiz_updated_at;
        return $this->embed_quiz_updated_at;
    }

    protected function set_embed_quiz_is_dev($embed_quiz_is_dev) {
        $this->embed_quiz_is_dev = $embed_quiz_is_dev;
        return $this->embed_quiz_is_dev;
    }

    public function get_embed_quiz_id() {
        return $this->embed_quiz_id;
    }

    public function get_embed_site_id() {
        return $this->embed_site_id;
    }

    public function get_quiz_id() {
        return $this->quiz_id;
    }

    public function get_embed_quiz_url() {
        return $this->embed_quiz_url;
    }

    public function get_embed_quiz_created_at() {
        return $this->embed_quiz_created_at;
    }

    public function get_embed_quiz_updated_at() {
        return $this->embed_quiz_updated_at;
    }

    public function get_embed_quiz_is_dev() {
        return $this->embed_quiz_is_dev;
    }

}
