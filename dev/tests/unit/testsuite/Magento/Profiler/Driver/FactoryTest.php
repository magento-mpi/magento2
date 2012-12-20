<?php
/**
 * Test class for Magento_Profiler_Driver_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Factory
     */
    protected $_factory;

    /**
     * @var string
     */
    protected $_defaultDriverPrefix = 'Magento_Profiler_Driver_Test_';

    /**
     * @var string
     */
    protected $_defaultDriverType = 'default';

    protected function setUp()
    {
        $this->_factory = new Magento_Profiler_Driver_Factory(
            $this->_defaultDriverPrefix,
            $this->_defaultDriverType
        );
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->_defaultDriverPrefix, '_defaultDriverPrefix', $this->_factory);
        $this->assertAttributeEquals($this->_defaultDriverType, '_defaultDriverType', $this->_factory);
    }

    public function testDefaultConstructor()
    {
        $factory = new Magento_Profiler_Driver_Factory();
        $this->assertAttributeNotEmpty('_defaultDriverPrefix', $factory);
        $this->assertAttributeNotEmpty('_defaultDriverType', $factory);
    }

    /**
     * @dataProvider createDataProvider
     * @param array $configData
     * @param string $expectedClass
     */
    public function testCreate($configData, $expectedClass)
    {
        $driver = $this->_factory->create(new Magento_Profiler_Driver_Configuration($configData));
        $this->assertInstanceOf($expectedClass, $driver);
        $this->assertInstanceOf('Magento_Profiler_DriverInterface', $driver);
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        $defaultDriverClass = $this->getMockClass(
            'Magento_Profiler_DriverInterface', array(), array(), 'Magento_Profiler_Driver_Test_Default'
        );
        $testDriverClass = $this->getMockClass(
            'Magento_Profiler_DriverInterface', array(), array(), 'Magento_Profiler_Driver_Test_Test'
        );
        return array(
            'Prefix and concrete type' => array(
                array(
                    'type' => 'test'
                ),
                $testDriverClass
            ),
            'Prefix and default type' => array(
                array(),
                $defaultDriverClass
            ),
            'Concrete class' => array(
                array(
                    'type' => $testDriverClass
                ),
                $testDriverClass
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot create profiler driver, class "Magento_Profiler_Driver_Test_Baz" doesn't exist.
     */
    public function testCreateUndefinedClass()
    {
        $this->_factory->create(new Magento_Profiler_Driver_Configuration(array(
            'type' => 'baz'
        )));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Driver class "stdClass" must implement Magento_Profiler_DriverInterface.
     */
    public function testCreateInvalidClass()
    {
        $this->_factory->create(new Magento_Profiler_Driver_Configuration(array(
            'type' => 'stdClass'
        )));
    }
}
