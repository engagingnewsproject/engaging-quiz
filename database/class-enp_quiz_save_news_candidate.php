<?php
/**
 * Persist news candidate review status (annotation layer only).
 *
 * @package Enp_quiz
 */
class Enp_quiz_Save_news_candidate extends Enp_quiz_Save {

    /** @var string */
    private $table;

    /** @var array<int, string> */
    private $allowed_statuses = array( 'pending', 'confirmed_news', 'exclude' );

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'enp_news_candidate';
    }

    /**
     * Upsert review_status and optional notes for an embed site.
     *
     * @param int    $embed_site_id
     * @param string $normalized_host
     * @param string $review_status
     * @param string $notes
     * @return array<string, mixed>
     */
    public function save_review( $embed_site_id, $normalized_host, $review_status, $notes = '' ) {
        $this->response = array( 'error' => array(), 'success' => array() );

        $embed_site_id = (int) $embed_site_id;
        if ( $embed_site_id <= 0 ) {
            $this->add_error( 'Invalid embed_site_id' );
            return $this->response;
        }

        $normalized_host = sanitize_text_field( (string) $normalized_host );
        if ( $normalized_host === '' ) {
            $this->add_error( 'normalized_host is required' );
            return $this->response;
        }

        $review_status = sanitize_text_field( (string) $review_status );
        if ( ! in_array( $review_status, $this->allowed_statuses, true ) ) {
            $this->add_error( 'Invalid review_status' );
            return $this->response;
        }

        $notes = sanitize_textarea_field( (string) $notes );
        $now = gmdate( 'Y-m-d H:i:s' );

        global $wpdb;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table}'" ) !== $this->table ) {
            $this->add_error( 'News candidate table is not installed' );
            return $this->response;
        }

        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT embed_site_id FROM {$this->table} WHERE embed_site_id = %d",
                $embed_site_id
            )
        );

        if ( $existing ) {
            $wpdb->update(
                $this->table,
                array(
                    'normalized_host' => $normalized_host,
                    'review_status'   => $review_status,
                    'notes'           => $notes,
                    'updated_at'      => $now,
                ),
                array( 'embed_site_id' => $embed_site_id ),
                array( '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );
        } else {
            $wpdb->insert(
                $this->table,
                array(
                    'embed_site_id'   => $embed_site_id,
                    'normalized_host' => $normalized_host,
                    'review_status'   => $review_status,
                    'notes'           => $notes,
                    'updated_at'      => $now,
                ),
                array( '%d', '%s', '%s', '%s', '%s' )
            );
        }

        $this->response['embed_site_id'] = $embed_site_id;
        $this->response['review_status'] = $review_status;
        $this->response['notes'] = $notes;
        $this->add_success( 'Review status saved' );
        return $this->response;
    }
}
