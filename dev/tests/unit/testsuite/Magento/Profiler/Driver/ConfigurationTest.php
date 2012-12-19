<?php
/**
 * Test class for Magento_Profiler_Driver_Configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Configuration
     */
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Magento_Profiler_Driver_Configuration();
    }

    /**
     * @dataProvider getValueDataProvider
     * @param string $getterMethod
     * @param mixed $expectedValue
     * @param mixed $actualValue
     * @param mixed $default
     * @return void
     */
    public function testGetValue($getterMethod, $expectedValue, $actualValue, $default = null)
    {
        $name = 'test';
        $configuration = new Magento_Profiler_Driver_Configuration(array(
            $name => $actualValue,
        ));
        $this->assertEquals($expectedValue, $configuration->$getterMethod($name, $default));
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        $object = $this->getMock('Foo', array('__toString'));
        $object->expects($this->any())->method('__toString')->will($this->returnValue('foo'));
        return array(
            'getValue' => array(
                'getValue',
                'string',
                'string',
            ),
            'getScalarValue, int' => array(
                'getScalarValue', 1, 1,
            ),
            'getScalarValue, array, default value' => array(
                'getScalarValue', null, array(1)
            ),
            'getScalarValue, array, custom default value' => array(
                'getScalarValue', 1, array(1), 1
            ),
            'getIntegerValue, int' => array(
                'getIntegerValue', 1, 1,
            ),
            'getIntegerValue, string, custom default value' => array(
                'getIntegerValue', 100, 'value', 100
            ),
            'getFloatValue, float' => array(
                'getFloatValue', 196.5, 196.5,
            ),
            'getFloatValue, string, custom default value' => array(
                'getFloatValue', 10.56, 'value', 10.56
            ),
            'getBoolValue, string' => array(
                'getBoolValue', true, 'value'
            ),
            'getBoolValue, bool' => array(
                'getBoolValue', false, false
            ),
            'getStringValue, string' => array(
                'getStringValue', 'String Value', 'String Value'
            ),
            'getStringValue, array, custom default value' => array(
                'getStringValue', 'String Value', array(1), 'String Value'
            ),
            'getStringValue, object, __toString' => array(
                'getStringValue', $object, 'foo'
            ),
            'getArrayValue, array' => array(
                'getArrayValue', array(1, 2, 3), array(1, 2, 3), array(),
            ),
            'getArrayValue, string' => array(
                'getArrayValue', array(), 'string', array(),
            )
        );
    }

    public function testHasTypeValue()
    {
        $this->assertFalse($this->_configuration->hasTypeValue());
        $this->_configuration->setTypeValue('test');
        $this->assertTrue($this->_configuration->hasTypeValue());
    }

    public function testGetAndSetTypeValue()
    {
        $this->assertEquals('default', $this->_configuration->getTypeValue('default'));
        $this->_configuration->setTypeValue('test');
        $this->assertEquals('test', $this->_configuration->getTypeValue());
    }

    public function testHasBaseDirValue()
    {
        $this->assertFalse($this->_configuration->hasBaseDirValue());
        $this->_configuration->setBaseDirValue('test');
        $this->assertTrue($this->_configuration->hasBaseDirValue());
    }

    public function testGetAndSetBaseDirValue()
    {
        $this->assertEquals('default', $this->_configuration->getBaseDirValue('default'));
        $this->_configuration->setBaseDirValue('test');
        $this->assertEquals('test', $this->_configuration->getBaseDirValue());
    }
}
