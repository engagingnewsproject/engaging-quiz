<?php

class Enp_quiz_Upgrade {
    public $old_version;

    public function __construct($old_version) {
        // see which upgrades we should run

        // if current (old) version is less than 1.0.1
        if(version_compare("1.0.1", $old_version) === 1) {
            $this->resave_quizzes();
        }

        // if current (old) version is less than 1.1.0
        if(version_compare("1.1.0", $old_version) === 1) {
            $this->upgrade_to_110();
        }

        // update to the new version
        update_option('enp_quiz_version', ENP_QUIZ_VERSION);
    }


    public function upgrade_to_110() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-activator.php';
        // run activation scripts to create our new tables
        $activator = new Enp_quiz_Activator();
        $activator->create_tables();
    }

    /**
    * Grabs all quizzes and resaves them so they get rebuilt in the DB with any new
    * options, etc
    */
    public function resave_quizzes() {
        // get all the quizzes
        $quiz_ids = $this->get_all_quiz_ids();
        // PDO to grab ALL unique Quiz IDs
        $save_quiz = new Enp_quiz_Save_quiz();

        // query to get ALL quizzes
        // for now,  just do one
        if(!empty($quiz_ids)) {
            foreach($quiz_ids as $quiz_id) {

                $quiz = new Enp_quiz_Quiz($quiz_id);
                // turn it into an array
                $quiz = (array) $quiz;
                // we have to set this as the quiz created by value so it will allow us to update
                // in the future, we can set it to an admin value once we allow that
                $quiz['quiz_updated_by'] = $quiz['quiz_owner'];
                $quiz['quiz_updated_at'] = date("Y-m-d H:i:s");
                // save the quiz
                $save_quiz->save($quiz);

            }
        }
    }

    /**
    * Selects all quiz ids from the DB
    * @return array of all quiz ids
    */
    public function get_all_quiz_ids() {
        $pdo = new enp_quiz_Db();
        $sql = "SELECT quiz_id from ".$pdo->quiz_table;
        $stmt = $pdo->query($sql);
        $quiz_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $quiz_ids;
    }

}
