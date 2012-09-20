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
     * @dataProvider dataProviderForTestPrepareMethodParams
     * @param string|object $class
     * @param string $methodName
     * @param array $requestData
     * @param array $expectedResult
     * @param bool $isExceptionExpected
     */
    public function testPrepareMethodParams($class, $methodName, $requestData,
        $expectedResult = array(), $isExceptionExpected = false
    ) {
        if ($isExceptionExpected) {
            $this->setExpectedException('RuntimeException', 'Required parameter "%s" is missing.');
        }
        $actualResult = $this->_helper->prepareMethodParams($class, $methodName, $requestData);
        $this->assertSame($expectedResult, $actualResult, "The array of arguments was prepared incorrectly.");
    }

    public static function dataProviderForTestPrepareMethodParams()
    {
        return array(
            // Test valid data that does not need transformations
            array(
                'ClassForReflectionTesting',
                'doSomething1',
                array('param1' => 1, 'param2' => 2, 'param3' => array(3), 'param4' => 4),
                array('param1' => 1, 'param2' => 2, 'param3' => array(3), 'param4' => 4),
            ),
            // Test filtering unnecessary data
            array(
                'ClassForReflectionTesting',
                'doSomething2',
                array('param1' => 1, 'param2' => 2, 'param3' => array(3), 'param4' => 4),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test exception in case of missing required parameter
            array(
                'ClassForReflectionTesting',
                'doSomething1',
                array('param2' => 2, 'param4' => 4),
                null,
                true
            ),
            // Test parameters sorting
            array(
                'ClassForReflectionTesting',
                'doSomething1',
                array('param4' => 4, 'param2' => 2, 'param3' => array(3), 'param1' => 1),
                array('param1' => 1, 'param2' => 2, 'param3' => array(3), 'param4' => 4),
            ),
            // Test default values setting
            array(
                'ClassForReflectionTesting',
                'doSomething1',
                array('param1' => 1, 'param2' => 2),
                array('param1' => 1, 'param2' => 2, 'param3' => array(), 'param4' => 'default_value'),
            ),
            // Test with object instead of class name
            array(
                new ClassForReflectionTesting(),
                'doSomething2',
                array('param2' => 2, 'param1' => 1),
                array('param1' => 1, 'param2' => 2),
            ),
        );
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
