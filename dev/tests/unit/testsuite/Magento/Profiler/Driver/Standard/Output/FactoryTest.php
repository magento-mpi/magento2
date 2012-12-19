<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_Output_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_Output_Factory
     */
    protected $_factory;

    /**
     * @var string
     */
    protected $_defaultOutputPrefix = 'Magento_Profiler_Driver_Standard_Output_Test_';

    /**
     * @var string
     */
    protected $_defaultOutputType = 'default';

    protected function setUp()
    {
        $this->_factory = new Magento_Profiler_Driver_Standard_Output_Factory(
            $this->_defaultOutputPrefix,
            $this->_defaultOutputType
        );
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->_defaultOutputPrefix, '_defaultOutputClassPrefix', $this->_factory);
        $this->assertAttributeEquals($this->_defaultOutputType, '_defaultOutputType', $this->_factory);

        $factory = new Magento_Profiler_Driver_Standard_Output_Factory();
        $this->assertAttributeNotEmpty('_defaultOutputClassPrefix', $factory);
        $this->assertAttributeNotEmpty('_defaultOutputType', $factory);
    }

    /**
     * @dataProvider createDataProvider
     * @param array $configData
     * @param string $expectedClass
     */
    public function testCreate($configData, $expectedClass)
    {
        $driver = $this->_factory->create(new Magento_Profiler_Driver_Standard_Output_Configuration($configData));
        $this->assertInstanceOf($expectedClass, $driver);
        $this->assertInstanceOf('Magento_Profiler_Driver_Standard_OutputInterface', $driver);
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        $defaultOutputClass = $this->getMockClass('Magento_Profiler_Driver_Standard_OutputInterface',
            array(), array(), 'Magento_Profiler_Driver_Standard_Output_Test_Default'
        );
        $testOutputClass = $this->getMockClass('Magento_Profiler_Driver_Standard_OutputInterface',
            array(), array(), 'Magento_Profiler_Driver_Standard_Output_Test_Test'
        );
        return array(
            'Prefix and concrete type' => array(
                array(
                    'type' => 'test'
                ),
                $testOutputClass
            ),
            'Prefix and default type' => array(
                array(),
                $defaultOutputClass
            ),
            'Concrete class' => array(
                array(
                    'type' => $testOutputClass
                ),
                $testOutputClass
            )
        );
    }

    public function testCreateUndefinedClass()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf(
                'Cannot create standard driver output, class "%s" doesn\'t exist.',
                'Magento_Profiler_Driver_Standard_Output_Test_Baz'
            )
        );
        $this->_factory->create(new Magento_Profiler_Driver_Standard_Output_Configuration(array(
            'type' => 'baz'
        )));
    }

    public function testCreateInvalidClass()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Output class "stdClass" must implement Magento_Profiler_Driver_Standard_OutputInterface.'
        );
        $this->_factory->create(new Magento_Profiler_Driver_Standard_Output_Configuration(array(
            'type' => 'stdClass'
        )));
    }
}
