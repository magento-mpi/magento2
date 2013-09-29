<?php
/**
 * Test class for \Magento\Profiler\Driver\Standard\Output\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler\Driver\Standard\Output;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Profiler\Driver\Standard\Output\Factory
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
        $this->_factory = new \Magento\Profiler\Driver\Standard\Output\Factory(
            $this->_defaultOutputPrefix,
            $this->_defaultOutputType
        );
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->_defaultOutputPrefix, '_defaultOutputPrefix', $this->_factory);
        $this->assertAttributeEquals($this->_defaultOutputType, '_defaultOutputType', $this->_factory);
    }

    public function testDefaultConstructor()
    {
        $factory = new \Magento\Profiler\Driver\Standard\Output\Factory();
        $this->assertAttributeNotEmpty('_defaultOutputPrefix', $factory);
        $this->assertAttributeNotEmpty('_defaultOutputType', $factory);
    }

    /**
     * @dataProvider createDataProvider
     * @param array $configData
     * @param string $expectedClass
     */
    public function testCreate($configData, $expectedClass)
    {
        $driver = $this->_factory->create($configData);
        $this->assertInstanceOf($expectedClass, $driver);
        $this->assertInstanceOf('Magento\Profiler\Driver\Standard\OutputInterface', $driver);
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        $defaultOutputClass = $this->getMockClass('Magento\Profiler\Driver\Standard\OutputInterface',
            array(), array(), 'Magento_Profiler_Driver_Standard_Output_Test_Default'
        );
        $testOutputClass = $this->getMockClass('Magento\Profiler\Driver\Standard\OutputInterface',
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
        $this->_factory->create(array(
            'type' => 'baz'
        ));
    }

    public function testCreateInvalidClass()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Output class "stdClass" must implement \Magento\Profiler\Driver\Standard\OutputInterface.'
        );
        $this->_factory->create(array(
            'type' => 'stdClass'
        ));
    }
}
