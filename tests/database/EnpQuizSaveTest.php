<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers Enp_quiz_Save
 */
final class EnpQuizSaveTest extends EnpTestCase
{
    protected static $enp_save;

    protected function setUp()
    {
        $this->enpSetUp();
        self::$enp_save = new Enp_quiz_Save();
    }

    public function tearDown() {
      $this->enpTearDown();
    }

    /**
     * @covers Enp_quiz_Save->is_id()
     * @dataProvider testValidateIDProvider
     */
    public function testValidateID($id, $shouldReturn) {
        // $enp_save = new Enp_quiz_Save();
        $valid = self::$enp_save->is_id($id);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testValidateIDProvider() {
        return [
                'valid-1'=>['123456789', true],
                'valid-2'=>[1, true],
                'valid-3'=>['999', true],
                'invalid-1'=>['0', false],
                'invalid-2'=>['h123', false],
                'invalid-3'=>['!12', false],
                'invalid-4'=>['', false],
                'invalid-5'=>[true, false],
                'invalid-6'=>[false, false]
        ];
    }


    /**
     * @covers Enp_quiz_Save->validate_hex()
     * @dataProvider testValidateHexProvider
     */
    public function testValidateHex($hex, $shouldReturn) {
        $valid = self::$enp_save->validate_hex($hex);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testValidateHexProvider() {
        return [
                'invalid-1' => ['#0000', false],
                'invalid-2' => ['#jkjkjk',false],
                'invalid-3' => ['000',false],
                'invalid-4' => ['111111',false],
                'black' => ['#000', true],
                'orange' => ['#fc0', true],
                'full-black' => ['#111111', true]
            ];
    }


    /**
     * @covers Enp_quiz_Save->has_errors()
     * @dataProvider testHasErrorsProvider
     */
    public function testHasErrors($response, $shouldReturn) {
        $valid = self::$enp_save->has_errors($response);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testHasErrorsProvider() {
        return [
                'no-errors' => [array('error'=>array()), false],
                'has-errors' => [array('error'=>array('hi! I am an error')), true],
            ];
    }

    /**
     * @covers Enp_quiz_Save->does_quiz_exist()
     * @dataProvider testQuizExistsProvider
     */
    public function testQuizExists($id, $shouldReturn) {
        $valid = self::$enp_save->does_quiz_exist($id);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testQuizExistsProvider() {
        return [
                'valid-1'=>['1', true],
                'valid-2'=>[2, true],
                'valid-3'=>['3', true],
                'invalid-1'=>['99999999999999', false],
                'invalid-2'=>['hi', false],
                'invalid-3'=>[false, false],
                'invalid-4'=>[999999999999999, false]
        ];
    }

    /**
     * @covers Enp_quiz_Save->is_valid_url()
     * @dataProvider testIsValidURLProvider
     */
    public function testIsValidURL($url, $shouldReturn) {
        $valid = self::$enp_save->is_valid_url($url);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testIsValidURLProvider() {
        return [
                'valid-1'=>['http://jeremyjon.es', true],
                'valid-2'=>['https://www.engagingnewsproject.org', true],
                'valid-3'=>['http://nytimes.com', true],
                'invalid-1'=>['99999999999999', false],
                'invalid-2'=>['dev.dev.dev', false],
                'invalid-3'=>['www.fail.com', false],
        ];
    }

    /**
     * @covers Enp_quiz_Save->is_slug()
     * @dataProvider testIsSlugProvider
     */
    public function testIsSlug($slug, $shouldReturn) {
        $valid = self::$enp_save->is_slug($slug);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testIsSlugProvider() {
        return [
                'valid-1'=>['yes', true],
                'valid-2'=>['wut-up-dawg', true],
                'valid-3'=>['of-curze-123', true],
                'invalid-1'=>['NoPe', false],
                'invalid-2'=>['notaslug!!!!', false],
                'invalid-3'=>[0, false],
                'invalid-4'=>[1, false],
                'invalid-5'=>['', false],
                'invalid-6'=>[true, false],
                'invalid-7'=>['-hi', false],
                'invalid-8'=>['hi-', false],
        ];
    }

    /**
     * @dataProvider testDoesEmbedSiteExistProvider
     * @covers Enp_quiz_Save->does_embed_site_exist()
     */
    public function testDoesEmbedSiteExist($query, $shouldReturn) {
        $valid = self::$enp_save->does_embed_site_exist($query);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testDoesEmbedSiteExistProvider() {
        return [
                'valid-1'=>['http://local.quiz', true],
                'valid-2'=>['1', true],
                'valid-3'=>['http://jeremyjon.es', true],
                'invalid-1'=>['99999999999999', false],
                'invalid-2'=>['http://newyorktimes.com', false],
                'invalid-3'=>[false, false],
                'invalid-4'=>[true, false]
        ];
    }

    /**
     * @dataProvider testDoesEmbedQuizExistProvider
     * @covers Enp_quiz_Save->does_embed_quiz_exist()
     */
    public function testDoesEmbedQuizExist($query, $shouldReturn) {
        $valid = self::$enp_save->does_embed_quiz_exist($query);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testDoesEmbedQuizExistProvider() {
        return [
                'valid-1'=>['https://jeremyjon.es/quizzes', true],
                'valid-2'=>['1', true],
                'valid-3'=>['http://localhost:3000/2017/05/25/sample-quiz/', true],
                'invalid-1'=>['99999999999999', false],
                'invalid-2'=>['http://newyorktimes.com', false],
                'invalid-3'=>[false, false],
                'invalid-4'=>[true, false]
        ];
    }

    /**
     * @covers Enp_quiz_Save->is_date()
     * @dataProvider testValidateDateProvider
     */
    public function testValidateDate($date, $shouldReturn) {
        // $enp_save = new Enp_quiz_Save();
        $valid = self::$enp_save->is_date($date);
        $this->evaluateAssert($valid, $shouldReturn);
    }

    public function testValidateDateProvider() {
        return [
                'valid-1'=>['2017-05-31 21:33:50', true],
                'valid-2'=>['2018-05-31 00:00:00', true],
                'invalid-1'=>['0', false],
                'invalid-2'=>['2016-04-31', false],
                'invalid-3'=>['2019/12/01 00:00:00', false],
                'invalid-4'=>['2018.05.31 00:00:00', false],
                'invalid-5'=>['2018-05-31 25:00:00', false],
                'invalid-6'=>[true, false],
                'invalid-7'=>[false, false]
        ];
    }

}
