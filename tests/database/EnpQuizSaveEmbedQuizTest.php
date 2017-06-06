<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers Enp_quiz_Save
 */
final class EnpQuizSaveEmbedQuizTest extends EnpTestCase
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



}
