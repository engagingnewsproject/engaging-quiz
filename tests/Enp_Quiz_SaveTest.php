<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers Enp_quiz_Save
 */
final class Enp_quiz_SaveTest extends TestCase
{
    protected static $enp_save;

    protected function setUp()
    {
        self::$enp_save = new Enp_quiz_Save();
    }

    public function evaluateAssert($val, $shouldReturn) {
        if($shouldReturn === false) {
            $this->assertFalse($val);
        } else {
            $this->assertTrue($val);
        }
    }

    /**
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
                'invalid-4'=>['', false]
        ];
    }

    public function testInvalidID() {
        $id = '123hi';
        $valid = self::$enp_save->is_id($id);
        $this->assertFalse($valid);
    }

    /**
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


}
