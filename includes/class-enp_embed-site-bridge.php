<?/**
 * Bridge IDs between embed_site and embed_site_type
 * to show which sites are in a category and which categories belong to a site
 *
 * @since      1.0.1
 * @package    Enp_quiz
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Embed_site_bridge {
    public  $embed_site_bridge_site,
            $embed_site_bridge_type;

    public function __construct($type, $id) {
        $this->get_embed_site_bridge_by($type, $id);
    }

    /**
    *   Build embed_site object by slug or id
    *
    *   @param  $selector (MIXED: STRING/INT) $slug or $id
    *   @return embed_site_type object, false if not found
    **/
    public function get_embed_site_bridge_by($type, $id) {

        $embed_site_bridge = false;
        // if it's one of our allowed types, get it!
        if($type === 'site') {
            $embed_site_bridge = $this->select_embed_site_bridge_by_site($id);
        } else if($type === 'type') {
            $embed_site_bridge = $this->select_embed_site_bridge_by_type($id);
        }

        if($embed_site_bridge !== false) {
            $embed_site_bridge = $this->set_embed_site_bridge_object_values_by($type, $embed_site_bridge);
        }
        return $embed_site_bridge;
    }

    /**
    *   For using PDO to select all a site's rows
    *
    *   @param  $site_id = site that you want to find categories for
    *   @return rows from database table if found, false if not found
    **/
    protected function select_embed_site_bridge_by_site($site_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":site_id" => $site_id
        );

        // there *should* only be one since embed_syte_type is a unique column
        $sql = "SELECT * from ".$pdo->embed_site_br_site_type_table." WHERE
                embed_site_id = :site_id";
        $stmt = $pdo->query($sql, $params);
        $embed_bridge_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return the found rows
        return $embed_bridge_rows;
    }

    /**
    *   For using PDO to select all a site's rows
    *
    *   @param  $site_id = site that you want to find categories for
    *   @return rows from database table if found, false if not found
    **/
    protected function select_embed_site_bridge_by_type($type_id) {
        $pdo = new enp_quiz_Db();
        // Do a select query to see if we get a returned row
        $params = array(
            ":type_id" => $type_id
        );

        // there *should* only be one since embed_syte_type is a unique column
        $sql = "SELECT * from ".$pdo->embed_site_br_site_type_table." WHERE
                embed_site_type_id = :type_id";
        $stmt = $pdo->query($sql, $params);
        $embed_bridge_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return the found rows
        return $embed_bridge_rows;
    }

    /**
    * Sets all object variables
    */
    protected function set_embed_site_bridge_object_values_by($type, $embed_bridge_rows) {

        if($type === 'site') {
            $site_id = $embed_bridge_rows[0]['embed_site_id'];
            $type_id = $this->extract_values_from_rows('embed_site_type_id', $embed_bridge_rows);
        } else if($type === 'type') {
            $type_id = $embed_bridge_rows[0]['embed_site_type_id'];
            $site_id = $this->extract_values_from_rows('embed_site_id', $embed_bridge_rows);
        }
        $this->set_embed_site_bridge_site($site_id);
        $this->set_embed_site_bridge_type($type_id);
    }

    /**
    * Get all the values of a key and and put them into an array together
    *
    * @param $key STRING
    * @param $rows ARRAY
    * @return ARRAY of all values by key
    */
    protected function extract_values_from_rows($key, $rows) {
        $vals = array();
        foreach($rows as $row) {
            if(array_key_exists($key, $row)) {
                $vals[] = $row[$key];
            }
        }
        return $vals;
    }


    protected function set_embed_site_bridge_site($embed_site_bridge_site) {
        $this->embed_site_bridge_site = $embed_site_bridge_site;
        return $this->embed_site_bridge_site;
    }

    protected function set_embed_site_bridge_type($embed_site_bridge_type) {
        $this->embed_site_bridge_type = $embed_site_bridge_type;
        return $this->embed_site_bridge_type;
    }

    public function get_embed_site_bridge_site() {
        return $this->embed_site_bridge_site;
    }

    public function get_embed_site_bridge_type() {
        return $this->embed_site_bridge_type;
    }

    // helper functions
    public function get_sites() {
        return $this->get_embed_site_bridge_site();
    }

    public function get_types() {
        return $this->get_embed_site_bridge_type();
    }

}
