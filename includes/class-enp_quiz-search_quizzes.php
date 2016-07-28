<?php
/**
* A little utility class for searching quizzes
*/
class Enp_quiz_Search_quizzes {
    private $_type;
    public $search = '', // string
           $include = 'user', // 'all_users'
           $order_by = 'quiz_created_at',
           $status = '',
           $order = 'DESC',
           $page = '1',
           $limit = '30',
           $deleted = '0';

    public function __construct() {
        $this->_type = $this->set_type();
    }

    private function set_type() {
        if(current_user_can('manage_options')) {
            $_type = 'admin';
        } else {
            $_type = 'user';
        }

        return $_type;
    }

    public function set_order_by($str) {
        $this->order = 'ASC';
        if($str === 'DESC') {
            $this->order = 'DESC';
        }
    }

    public function set_include($str) {
        $this->include = 'user';
        if($str === 'all_users' && $this->_type === 'admin') {
            $this->include = 'all_users';
        }
    }

    public function set_search($str) {
        $this->search = $str;
    }

    public function set_variables_from_url_query() {
        // check for variables
        if(isset($_GET['include']) && $_GET['include']==='all_users') {
            // get from ALL users
            $this->set_include('all_users');
        }

        // get search variable
        if(isset($_GET['search']) && $_GET['search'] !== '') {
            $this->set_search($_GET['search']);
        }

    }

    public function select_quizzes() {
        $pdo = new enp_quiz_Db();

        $status_sql = $this->get_status_sql();
        $search_sql = $this->get_search_sql($pdo);
        $include_sql = $this->get_include_sql();

        $sql = "SELECT quiz_id from $pdo->quiz_table
                WHERE quiz_is_deleted = $this->deleted
                $status_sql
                $search_sql
                $include_sql
                ORDER BY $this->order_by $this->order";
        $stmt = $pdo->query($sql);
        $quiz_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $quiz_ids;
    }

    public function get_status_sql() {
        $status_sql = '';
        if($this->status !=='') {
            $status_sql =  " AND quiz_status = '$this->status'";
        }
        return $status_sql;
    }

    public function get_search_sql($pdo) {
        $search_sql = '';

        if($this->search !== '') {
            // make it a wildcard string
            $search_string = '%'.$this->search.'%';
            // quote it
            $quoted_string = $pdo->quote($search_string);
            // build the sql
            $search_sql = " AND quiz_title LIKE $quoted_string";
        }

        return $search_sql;
    }

    protected function get_include_sql() {
        if($this->_type === 'admin' && $this->include === 'all_users') {
            $include_sql = '';
        } else {
            $include_sql = ' AND quiz_created_by = '.get_current_user_id();
        }

        return $include_sql;
    }


}
