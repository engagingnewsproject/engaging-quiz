<?php
/**
 * Computes news candidate embed sites from live SQL passes and merges review state.
 *
 * @package Enp_quiz
 */
class Enp_quiz_News_candidates {

    /** @var enp_quiz_Db */
    private $db;

    /** @var string */
    private $review_table;

    public function __construct() {
        $this->db = new enp_quiz_Db();
        global $wpdb;
        $this->review_table = $wpdb->prefix . 'enp_news_candidate';
    }

    /**
     * Normalize embed site URL to host key (matches Python norm_host / quiz-data hosts.js).
     *
     * @param string $url
     * @return string
     */
    public static function normalize_host( $url ) {
        $u = trim( (string) $url );
        if ( $u === '' ) {
            return '';
        }
        if ( ! preg_match( '#^https?://#i', $u ) ) {
            $u = 'http://' . $u;
        }
        $host = parse_url( $u, PHP_URL_HOST );
        if ( ! $host ) {
            return '';
        }
        $host = strtolower( $host );
        if ( strpos( $host, 'www.' ) === 0 ) {
            $host = substr( $host, 4 );
        }
        return $host;
    }

    /**
     * SQL expression for normalized host from embed_site_url column alias s.
     *
     * @return string
     */
    public static function sql_normalized_host( $alias = 's' ) {
        return "REPLACE(
            SUBSTRING_INDEX(
                REPLACE(REPLACE(LOWER(TRIM({$alias}.embed_site_url)), 'https://', ''), 'http://', ''),
                '/', 1
            ),
            'www.', ''
        )";
    }

    /**
     * Shared global noise filter WHERE fragment for Pass 1.
     *
     * @param string $alias
     * @return string
     */
    public static function sql_global_noise_filter( $alias = 's' ) {
        $url = "LOWER({$alias}.embed_site_url)";
        $parts = array(
            "{$url} NOT LIKE '%localhost%'",
            "{$url} NOT LIKE '%127.0.0.1%'",
            "{$url} NOT LIKE '%stage.%'",
            "{$url} NOT LIKE '%stage-%'",
            "{$url} NOT LIKE '%dev.%'",
            "{$url} NOT LIKE '%preview.%'",
            "{$url} NOT LIKE '%beta-%'",
            "{$url} NOT REGEXP '[0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}'",
            "{$url} NOT LIKE '%.ml'",
            "{$url} NOT LIKE '%.cf'",
            "{$url} NOT LIKE '%.ga'",
            "{$url} NOT LIKE '%.tk'",
            "{$url} NOT LIKE '%netlify.app%'",
            "{$url} NOT LIKE '%herokuapp.com%'",
            "{$url} NOT LIKE '%wixsite.com%'",
            "{$url} NOT LIKE '%blogspot.com%'",
            "{$url} NOT LIKE '%wordpress.com%'",
            "{$url} NOT LIKE '%github.io%'",
            "{$url} NOT LIKE '%tumblr.com%'",
        );
        return implode( ' AND ', $parts );
    }

    /**
     * Base FROM/JOIN for site quiz aggregation queries.
     *
     * @return string
     */
    private function sql_site_quiz_join() {
        $site = $this->db->embed_site_table;
        $embed = $this->db->embed_quiz_table;
        $quiz = $this->db->quiz_table;
        return "FROM {$site} s
            INNER JOIN {$embed} eq ON eq.embed_site_id = s.embed_site_id
            INNER JOIN {$quiz} q ON q.quiz_id = eq.quiz_id AND q.quiz_is_deleted = 0";
    }

    /**
     * Run all inclusion passes and return merged candidate rows.
     *
     * @return array<int, array<string, mixed>>
     */
    public function compute_candidates() {
        $by_host = array();

        $passes = array(
            'original_148:high_volume' => array( $this, 'pass_1a_high_volume' ),
            'original_148:low_volume'  => array( $this, 'pass_1b_low_volume' ),
            'pass_2a_wicked_local' => array( $this, 'pass_2a_wicked_local' ),
            'pass_2b_nbc'         => array( $this, 'pass_2b_nbc' ),
            'pass_2b_ideastream'  => array( $this, 'pass_2b_ideastream' ),
            'pass_2b_gannett'     => array( $this, 'pass_2b_gannett' ),
        );

        foreach ( $passes as $tag => $callback ) {
            $rows = call_user_func( $callback );
            foreach ( $rows as $row ) {
                $host = self::normalize_host( $row['embed_site_url'] );
                if ( $host === '' ) {
                    continue;
                }
                $entry = array(
                    'embed_site_id'   => (int) $row['embed_site_id'],
                    'embed_site_url'  => $row['embed_site_url'],
                    'normalized_host' => $host,
                    'quiz_count'      => (int) $row['quiz_count'],
                    'owner_count'     => (int) $row['owner_count'],
                    'pass_tag'        => $tag,
                );
                if ( ! isset( $by_host[ $host ] ) ) {
                    $by_host[ $host ] = array();
                }
                $by_host[ $host ][] = $entry;
            }
        }

        return $this->merge_by_host( $by_host );
    }

    /**
     * Dedupe by normalized_host; tag inclusion_pass from all contributing passes.
     *
     * @param array<string, array<int, array<string, mixed>>> $by_host
     * @return array<int, array<string, mixed>>
     */
    public function merge_by_host( array $by_host ) {
        $merged = array();
        foreach ( $by_host as $host => $rows ) {
            $best = $rows[0];
            foreach ( $rows as $row ) {
                if ( $this->row_score( $row ) > $this->row_score( $best ) ) {
                    $best = $row;
                }
            }
            $passes = array();
            foreach ( $rows as $row ) {
                $passes[ $row['pass_tag'] ] = true;
            }
            $pass_list = array_keys( $passes );
            sort( $pass_list );
            $merged[] = array(
                'embed_site_id'   => $best['embed_site_id'],
                'embed_site_url'  => $best['embed_site_url'],
                'normalized_host' => $host,
                'quiz_count'      => $best['quiz_count'],
                'owner_count'     => $best['owner_count'],
                'inclusion_pass'  => implode( ';', $pass_list ),
            );
        }
        usort(
            $merged,
            function ( $a, $b ) {
                if ( $a['quiz_count'] !== $b['quiz_count'] ) {
                    return $b['quiz_count'] - $a['quiz_count'];
                }
                return strcmp( $a['normalized_host'], $b['normalized_host'] );
            }
        );
        return $merged;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<int, int>
     */
    private function row_score( array $row ) {
        $url = strtolower( $row['embed_site_url'] );
        return array(
            (int) $row['quiz_count'],
            strpos( $url, 'www.' ) !== false ? 1 : 0,
            (int) $row['owner_count'],
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_1a_high_volume() {
        $excludes = $this->exclude_hosts_sql( 'pass-1-excludes.php' );
        $noise = self::sql_global_noise_filter( 's' );
        $norm = self::sql_normalized_host( 's' );
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE {$noise}
            {$excludes}
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING quiz_count >= 50
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_1b_low_volume() {
        $excludes = $this->exclude_hosts_sql( 'pass-1-excludes.php' );
        $noise = self::sql_global_noise_filter( 's' );
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE {$noise}
            {$excludes}
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING owner_count >= 2 AND quiz_count >= 1 AND quiz_count <= 49
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_2a_wicked_local() {
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE LOWER(s.embed_site_url) LIKE '%wickedlocal.com%'
              AND LOWER(s.embed_site_url) NOT LIKE '%localhost%'
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING quiz_count >= 1
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_2b_nbc() {
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
              AND LOWER(s.embed_site_url) NOT LIKE '%stage.%'
              AND LOWER(s.embed_site_url) NOT LIKE '%dev.%'
              AND LOWER(s.embed_site_url) NOT LIKE '%preview.%'
              AND (
                LOWER(s.embed_site_url) REGEXP '://(www\\.)?nbc[a-z0-9-]+\\.com'
                OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?necn\\.com'
                OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?telemundo[0-9a-z-]+\\.com'
              )
              AND LOWER(s.embed_site_url) NOT LIKE '%nbcwpshield%'
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING quiz_count >= 1
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_2b_ideastream() {
        $hosts = require ENP_QUIZ_ROOT . 'includes/news-candidates/data/ideastream-hosts.php';
        $in = $this->quoted_in_list( $hosts );
        $norm = self::sql_normalized_host( 's' );
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
              AND {$norm} IN ({$in})
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING quiz_count >= 1
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pass_2b_gannett() {
        $hosts = require ENP_QUIZ_ROOT . 'includes/news-candidates/data/gannett-hosts.php';
        $in = $this->quoted_in_list( $hosts );
        $norm = self::sql_normalized_host( 's' );
        $sql = "SELECT s.embed_site_id, s.embed_site_url,
                COUNT(DISTINCT eq.quiz_id) AS quiz_count,
                COUNT(DISTINCT q.quiz_owner) AS owner_count
            {$this->sql_site_quiz_join()}
            WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
              AND {$norm} IN ({$in})
            GROUP BY s.embed_site_id, s.embed_site_url
            HAVING quiz_count >= 1
            ORDER BY quiz_count DESC";
        return $this->db->fetchAll( $sql );
    }

    /**
     * @param string $filename Basename under includes/news-candidates/data/
     * @return string SQL AND fragment
     */
    private function exclude_hosts_sql( $filename ) {
        $path = ENP_QUIZ_ROOT . 'includes/news-candidates/data/' . $filename;
        if ( ! file_exists( $path ) ) {
            return '';
        }
        $hosts = require $path;
        if ( empty( $hosts ) ) {
            return '';
        }
        $norm = self::sql_normalized_host( 's' );
        return ' AND ' . $norm . ' NOT IN (' . $this->quoted_in_list( $hosts ) . ')';
    }

    /**
     * @param array<int, string> $values
     * @return string
     */
    private function quoted_in_list( array $values ) {
        $quoted = array();
        foreach ( $values as $value ) {
            $quoted[] = $this->db->quote( (string) $value );
        }
        return implode( ', ', $quoted );
    }

    /**
     * Load review rows keyed by embed_site_id.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_review_map() {
        global $wpdb;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->review_table}'" ) !== $this->review_table ) {
            return array();
        }
        $sql = "SELECT embed_site_id, normalized_host, review_status, notes
            FROM {$this->review_table}";
        $rows = $this->db->fetchAll( $sql );
        $map = array();
        foreach ( $rows as $row ) {
            $map[ (int) $row['embed_site_id'] ] = $row;
        }
        return $map;
    }

    /**
     * Full API payload for GET /news-candidates.
     *
     * @return array<string, mixed>
     */
    public function get_api_payload() {
        $candidates = $this->compute_candidates();
        $reviews = $this->get_review_map();
        $out = array();

        foreach ( $candidates as $row ) {
            $site_id = (int) $row['embed_site_id'];
            $host = $row['normalized_host'];
            $review = isset( $reviews[ $site_id ] ) ? $reviews[ $site_id ] : null;
            $out[ $host ] = array(
                'embedSiteId'   => $site_id,
                'embedSiteUrl'  => $row['embed_site_url'],
                'quizCount'     => (int) $row['quiz_count'],
                'ownerCount'    => (int) $row['owner_count'],
                'inclusionPass' => $row['inclusion_pass'],
                'reviewStatus'  => $review ? $review['review_status'] : 'pending',
                'notes'         => $review ? $review['notes'] : '',
            );
        }

        return array(
            'count'      => count( $out ),
            'computedAt' => gmdate( 'c' ),
            'candidates' => $out,
        );
    }
}
