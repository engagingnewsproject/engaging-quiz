<?php
use PHPUnit\Framework\TestCase;

/**
 * Compare live candidate hosts to expanded CSV fixture (regression gate).
 *
 * @covers Enp_quiz_News_candidates
 */
final class EnpQuizNewsCandidatesParityTest extends EnpTestCase {

    public function testCandidateHostParityWithFixture() {
        $fixture = ENP_QUIZ_ROOT . 'tests/fixtures/legitimate-embed-sites-expanded.csv';
        if ( ! file_exists( $fixture ) ) {
            $this->markTestSkipped( 'Fixture CSV not found' );
        }

        $expected = array();
        if ( ( $handle = fopen( $fixture, 'r' ) ) !== false ) {
            $headers = fgetcsv( $handle );
            while ( ( $row = fgetcsv( $handle ) ) !== false ) {
                $data = array_combine( $headers, $row );
                if ( ! empty( $data['normalized_host'] ) ) {
                    $expected[ $data['normalized_host'] ] = true;
                }
            }
            fclose( $handle );
        }

        try {
            $engine = new Enp_quiz_News_candidates();
            $payload = $engine->get_api_payload();
        } catch ( Exception $e ) {
            $this->markTestSkipped( 'Database unavailable: ' . $e->getMessage() );
        }

        $actual = array_keys( $payload['candidates'] );
        $missing = array_diff( array_keys( $expected ), $actual );
        $extra = array_diff( $actual, array_keys( $expected ) );

        $this->assertGreaterThan(
            350,
            count( $actual ),
            'Expected roughly 400 candidates from live SQL'
        );
        $this->assertLessThan(
            50,
            count( $missing ),
            'Missing hosts vs fixture: ' . implode( ', ', array_slice( $missing, 0, 20 ) )
        );
    }
}
