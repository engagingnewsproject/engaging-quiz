<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers Enp_quiz_News_candidates
 */
final class EnpQuizNewsCandidatesTest extends EnpTestCase {

    public function testNormalizeHostStripsWww() {
        $this->assertSame(
            'nbcchicago.com',
            Enp_quiz_News_candidates::normalize_host( 'http://www.nbcchicago.com' )
        );
    }

    public function testNormalizeHostBareDomain() {
        $this->assertSame(
            'example.com',
            Enp_quiz_News_candidates::normalize_host( 'example.com' )
        );
    }

    public function testMergeByHostKeepsHighestQuizCount() {
        $engine = new Enp_quiz_News_candidates();
        $by_host = array(
            'example.com' => array(
                array(
                    'embed_site_id'   => 1,
                    'embed_site_url'  => 'http://example.com',
                    'quiz_count'      => 5,
                    'owner_count'     => 1,
                    'pass_tag'        => 'pass_2a_wicked_local',
                ),
                array(
                    'embed_site_id'   => 2,
                    'embed_site_url'  => 'http://www.example.com',
                    'quiz_count'      => 10,
                    'owner_count'     => 2,
                    'pass_tag'        => 'pass_2b_nbc',
                ),
            ),
        );
        $merged = $engine->merge_by_host( $by_host );
        $this->assertCount( 1, $merged );
        $this->assertSame( 2, $merged[0]['embed_site_id'] );
        $this->assertSame( 'pass_2a_wicked_local;pass_2b_nbc', $merged[0]['inclusion_pass'] );
    }
}
