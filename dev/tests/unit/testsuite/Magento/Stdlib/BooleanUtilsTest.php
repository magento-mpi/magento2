<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

class BooleanUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BooleanUtils
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new BooleanUtils();
    }

    public function testConstructor()
    {
        $object = new BooleanUtils(array('yep'), array('nope'));
        $this->assertTrue($object->toBoolean('yep'));
        $this->assertFalse($object->toBoolean('nope'));
    }

    /**
     * @param mixed $input
     * @param bool $expected
     *
     * @dataProvider toBooleanDataProvider
     */
    public function testToBoolean($input, $expected)
    {
        $actual = $this->object->toBoolean($input);
        $this->assertSame($expected, $actual);
    }

    public function toBooleanDataProvider()
    {
        return array(
            'boolean "true"'         => array(true, true),
            'boolean "false"'        => array(false, false),
            'boolean string "true"'  => array('true', true),
            'boolean string "false"' => array('false', false),
            'boolean numeric "1"'    => array(1, true),
            'boolean numeric "0"'    => array(0, false),
            'boolean string "1"'     => array('1', true),
            'boolean string "0"'     => array('0', false),
        );
    }

    /**
     * @param mixed $input
     *
     * @dataProvider toBooleanExceptionDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Boolean value is expected
     */
    public function testToBooleanException($input)
    {
        $this->object->toBoolean($input);
    }

    public function toBooleanExceptionDataProvider()
    {
        return array(
            'boolean string "on"'    => array('on'),
            'boolean string "off"'   => array('off'),
            'boolean string "yes"'   => array('yes'),
            'boolean string "no"'    => array('no'),
            'boolean string "TRUE"'  => array('TRUE'),
            'boolean string "FALSE"' => array('FALSE'),
            'empty string'           => array(''),
            'null'                   => array(null),
        );
    }
}
