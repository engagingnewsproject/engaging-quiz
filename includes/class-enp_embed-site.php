<?/**
 * Save to enp_embed_site table
 * Records what sites have embedded quizzes on them
 *
 * @since      1.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Save_embed_site {
    public  $embed_site_id,
            $embed_site_name,
            $embed_site_url,
            $embed_site_created_at,
            $embed_site_updated_at,
            $embed_site_is_dev;

    protected static $embed_site;

    public function __construct($url) {
        $this->get_embed_site_by_url($url);
    }

    /**
    *   Build embed_site object by url
    *
    *   @param  $url = url that you want to select from embed_site_url
    *   @return embed_site object, false if not found
    **/
    public function get_embed_site_by_url($url) {
        self::$embed_site = $this->select_embed_site_by_url($url);
        if(self::$embed_site !== false) {
            self::$embed_site = $this->set_embed_site_object_values();
        }
        return self::$embed_site;
    }

    /**
    *   For using PDO to select one quiz row
    *
    *   @param  $url = url that you want to select
    *   @return row from database table if found, false if not found
    **/
    public function select_embed_site_by_url($url) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":embed_site_url" => $url
        );
        $sql = "SELECT * from ".$pdo->embed_site_table." WHERE
                embed_site_url = :url";
        $stmt = $pdo->query($sql, $params);
        $quiz_row = $stmt->fetch();
        // return the found quiz row
        return $quiz_row;
    }

    protected function set_embed_site_name() {

    }

    public function get_embed_site_id() {
        return $this->site_name;
    }

    public function get_embed_site_name() {
        return $this->site_name;
    }

    public function get_embed_site_url() {
        return $this->site_url;
    }

    public function get_embed_site_created_at() {
        return $this->site_created_at;
    }

    public function get_embed_site_updated_at() {
        return $this->site_updated_at;
    }


}
