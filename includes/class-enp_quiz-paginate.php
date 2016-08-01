<?php
/**
* A little utility class for paginating
*/
class Enp_quiz_Paginate {
    public $page = '1',
           $offset = '0',
           $limit = '10',
           $total = '0',
           $url;

    public function __construct($total, $page, $limit, $url) {
        $this->current_page = (isset($_GET['page']) ? (int) $_GET['page'] : 1);
        $this->page = $page;
        $this->limit = $limit;
        $this->offset = ($page * $limit) - $limit;
        $this->total = ($total !== null ? $total : 0);
        $this->url = $this->remove_url_page_queries($url);

    }

    /**
    * Remove page=# queries from the url
    */
    public function remove_url_page_queries($url) {

        $url = preg_replace('/&?page=\S*?(&|(?=\/)|$)/', '', $url);

        return $url;
    }

    public function get_pagination_links() {
        if($this->total === 0) {
            return '';
        }


        $page_loop = 0;
        $page = 1;
        $total_pages = ceil($this->total/$this->limit);

        $pagination = '';
        
        if(1 < $total_pages ) {
            $pagination = '<ul class="enp-paginate">';
            while($page <= $total_pages) {
                $pagination .= $this->get_pagination_link($page);
                $page++;
            }
            $pagination .= '</ul>';
        }



        return $pagination;
    }

    public function get_pagination_link($page) {
        return '<li class="enp-paginate__item'.($this->current_page === $page ? ' enp-paginate__item--current-page':'').'"><a class="enp-paginate__link'.($this->current_page === $page ? ' enp-paginate__link--current-page':'').'" href="'.$this->url.'&page='.$page.'"><span class="enp-screen-reader-text">Quiz Page </span>'.$page.'</a></li>';
    }

}
