<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $this->_helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        parent::setUp();
    }

    /**
     * @dataProvider dataProviderForTestToArray
     * @param $objectToBeConverted
     * @param $expectedResult
     */
    public function testToArray($objectToBeConverted, $expectedResult)
    {
        $this->_helper->toArray($objectToBeConverted);
        $this->assertSame($expectedResult, $objectToBeConverted, "Object to array conversion failed.");
    }

    public static function dataProviderForTestToArray()
    {
        return array(
            // Test case without need in conversion
            array(
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3)),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3))
            ),
            // Test case with indexed array
            array(
                (object)array('key1' => 1, 'key2' => 'value2', 'key3' => array(3, 'value3')),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3, 'value3'))
            ),
            // Test mixed values in array
            array(
                array('key1' => 1, 'key2' => 'value2', 'key3' => (object)array('key3-1' => 'value3-1', 'key3-2' => 32)),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array('key3-1' => 'value3-1', 'key3-2' => 32))
            ),
            // Test recursive converting capabilities
            array(
                (object)array('key1' => array('key2' => (object)array('key3' => array('key4' => 'value4')))),
                array('key1' => array('key2' => array('key3' => array('key4' => 'value4'))))
            ),
        );
    }
}

class ClassForReflectionTesting
{
    public function doSomething1($param1, $param2, $param3 = array(), $param4 = 'default_value')
    {
        // Body is intentionally left empty
    }

    public function doSomething2($param1, $param2)
    {
        // Body is intentionally left empty
    }
}
