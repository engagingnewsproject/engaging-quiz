<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers Enp_quiz_Save_embed_quiz
 */
final class EnpQuizSaveEmbedQuizTest extends EnpTestCase
{
    protected static $save_embed_quiz;

    protected function setUp()
    {
        $this->enpSetUp();
        self::$save_embed_quiz = new Enp_quiz_Save_embed_quiz();
    }

    public function tearDown() {
      $this->enpTearDown();
    }

    /**
     * @covers Enp_quiz_Save_embed_quiz->save_embed_quiz()
     * @covers Enp_quiz_Save_embed_quiz->insert_embed_quiz()
     * @dataProvider saveEmbedQuizProvider
     */
    public function testSaveEmbedQuizInsert($embed_save, $shouldReturn) {
        $response = self::$save_embed_quiz->save_embed_quiz($embed_save);

        // if $response['embed_quiz_id'] exists
        // && $response['embed_quiz_id'] is a valid ID
        // && there are no errors
        // then it's valid = true
        if( array_key_exists('embed_quiz_id', $response) && self::$save_embed_quiz->is_id($response['embed_quiz_id']) && empty(self::$save_embed_quiz->has_errors($response)) ) {
            $valid = true;

        } else {
            $valid = false;
        }
        $this->evaluateAssert($valid, $shouldReturn);
    }

    /**
     * @covers Enp_quiz_Save_embed_quiz->validate_before_insert()
     * @dataProvider saveEmbedQuizProvider
     */
    public function testValidateBeforeInsert($embed_save, $shouldReturn) {
        $valid = self::$save_embed_quiz->validate_before_insert($embed_save);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function saveEmbedQuizProvider() {
        $date = date("Y-m-d H:i:s");

        return [
                'valid'=>[array(
                                'action'=>'insert',
                                'quiz_id'=>'1',
                                'embed_site_id'=>'1',
                                'embed_quiz_url'=>'http://jeremyjon.es/'.uniqid('test-case_'),
                                'embed_quiz_updated_at'=>$date
                            ),
                         true],
                'invalid_not_unique_quiz_id_and_embed_quiz_url_combo'=>[array(
                                    'action'=>'insert',
                                    'quiz_id'=>'1',
                                    'embed_site_id'=>'1',
                                    'embed_quiz_url'=>'http://jeremyjon.es/test-case',
                                    'embed_quiz_updated_at'=>$date
                                ),
                             false],
                'invalid_url'=>[array(
                                        'action'=>'insert',
                                        'quiz_id'=>'1',
                                        'embed_site_id'=>'1',
                                        'embed_quiz_url'=>'www.wut.com',
                                        'embed_quiz_updated_at'=>$date
                                    ),
                                 false],
                 'invalid_quiz_id'=>[array(
                                         'action'=>'insert',
                                         'quiz_id'=>'99999999999999',
                                         'embed_site_id'=>'1',
                                         'embed_quiz_url'=>'http://www.test.com',
                                         'embed_quiz_updated_at'=>$date
                                     ),
                                  false],
                  'invalid_site_id'=>[array(
                                          'action'=>'insert',
                                          'quiz_id'=>'1',
                                          'embed_site_id'=>'99999999999999',
                                          'embed_quiz_url'=>'http://www.test.com',
                                          'embed_quiz_updated_at'=>$date
                                      ),
                                   false],
                   'invalid_quiz_updated_at'=>[array(
                                           'action'=>'insert',
                                           'quiz_id'=>'1',
                                           'embed_site_id'=>'99999999999999',
                                           'embed_quiz_url'=>'http://www.test.com',
                                           'embed_quiz_updated_at'=>'2017-04-31 02:45:29'
                                       ),
                                    false],
                ];
    }



    /**
     * @covers Enp_quiz_Save_embed_quiz->update_embed_quiz_loads()
     * @dataProvider updateEmbedQuizProvider
     */
    public function testUpdateEmbedQuizLoadsInsert($embed_save, $shouldReturn) {
        $response = self::$save_embed_quiz->save_embed_quiz($embed_save);

        // if $response['embed_quiz_id'] exists
        // && $response['embed_quiz_id'] is a valid ID
        // && $response['status'] === 'success'
        // then it's valid = true
        if( array_key_exists('embed_quiz_id', $response) && self::$save_embed_quiz->is_id($response['embed_quiz_id']) &&
         array_key_exists('status', $response) && $response['status'] === 'success' && empty(self::$save_embed_quiz->has_errors($response)) ) {
            $valid = true;
        } else {
            $valid = false;
        }
        $this->evaluateAssert($valid, $shouldReturn);
    }

    /**
     * @covers Enp_quiz_Save_embed_quiz->save_embed_quiz()
     * @covers Enp_quiz_Save_embed_quiz->validate_before_save_load()
     * @dataProvider updateEmbedQuizProvider
     */
    public function testValidateBeforeSaveLoad($embed_save, $shouldReturn) {
        $valid = self::$save_embed_quiz->validate_before_save_load($embed_save);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function updateEmbedQuizProvider() {
        $date = date("Y-m-d H:i:s");

        return [
                'valid'=>[array(
                                'action'=>'save_load',
                                'embed_quiz_id'=>'1',
                                'embed_quiz_updated_at'=>$date
                            ),
                         true],
                'invalid_embed_quiz_id'=>[array(
                                      'action'=>'save_load',
                                      'embed_quiz_id'=>'99999999999999',
                                      'embed_quiz_updated_at'=>$date
                                  ),
                               false],
                'invalid_quiz_updated_at'=>[array(
                                       'action'=>'save_load',
                                       'embed_quiz_id'=>'1',
                                       'embed_quiz_updated_at'=>'2017-04-31 02:45:29'
                                   ),
                                false],
                ];
    }
}
