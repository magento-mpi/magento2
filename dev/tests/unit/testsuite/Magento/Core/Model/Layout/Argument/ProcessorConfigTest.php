<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Layout_Argument_ProcessorConfig
 */
class Magento_Core_Model_Layout_Argument_ProcessorConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_ProcessorConfig
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    protected function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Layout_Argument_ProcessorConfig(array(
            'objectFactory' => $this->_objectFactoryMock
        ));
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param $type
     * @expectedException InvalidArgumentException
     * @dataProvider getArgumentHandlerFactoryByTypeWithNonStringTypeDataProvider
     */
    public function testGetArgumentHandlerFactoryByTypeWithNonStringType($type)
    {
        $this->_model->getArgumentHandlerFactoryByType($type);
    }

    public function getArgumentHandlerFactoryByTypeWithNonStringTypeDataProvider()
    {
        return array(
            'int value' => array(10),
            'object value' => array(new StdClass()),
            'null value' => array(null),
            'boolean value' => array(false),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetArgumentHandlerFactoryByTypeWithInvalidType()
    {
        $this->_model->getArgumentHandlerFactoryByType('dummy_type');
    }

    /**
     * @param string $type
     * @param string $className
     * @dataProvider getArgumentHandlerFactoryByTypeWithValidTypeDataProvider
     */
    public function testGetArgumentHandlerFactoryByTypeWithValidType($type, $className)
    {
        $factoryMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_HandlerFactoryInterface',
            array(),
            array(),
            $className,
            false);
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with($className)
            ->will($this->returnValue($factoryMock));

        $this->assertInstanceOf($className, $this->_model->getArgumentHandlerFactoryByType($type));
    }

    public function getArgumentHandlerFactoryByTypeWithValidTypeDataProvider()
    {
        return array(
            'object'  => array('object', 'Magento_Core_Model_Layout_Argument_Handler_ObjectFactory'),
            'options' => array('options', 'Magento_Core_Model_Layout_Argument_Handler_OptionsFactory'),
            'url'     => array('url', 'Magento_Core_Model_Layout_Argument_Handler_UrlFactory')
        );
    }
}
