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
 * Test class for Magento_Core_Model_Layout_Argument_HandlerFactory
 */
class Magento_Core_Model_Layout_Argument_HandlerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_HandlerFactory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new Magento_Core_Model_Layout_Argument_HandlerFactory($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    /**
     * @param $type
     * @expectedException InvalidArgumentException
     * @dataProvider getArgumentHandlerFactoryByTypeWithNonStringTypeDataProvider
     */
    public function testGetArgumentHandlerByTypeWithNonStringType($type)
    {
        $this->_model->getArgumentHandlerByType($type);
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
        $this->_model->getArgumentHandlerByType('dummy_type');
    }

    /**
     * @param string $type
     * @param string $className
     * @dataProvider getArgumentHandlerFactoryByTypeWithValidTypeDataProvider
     */
    public function testGetArgumentHandlerFactoryByTypeWithValidType($type, $className)
    {
        $factoryMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className)
            ->will($this->returnValue($factoryMock));

        $this->assertInstanceOf($className, $this->_model->getArgumentHandlerByType($type));
    }

    public function getArgumentHandlerFactoryByTypeWithValidTypeDataProvider()
    {
        return array(
            'object'  => array('object', 'Magento_Core_Model_Layout_Argument_Handler_Object'),
            'options' => array('options', 'Magento_Core_Model_Layout_Argument_Handler_Options'),
            'url'     => array('url', 'Magento_Core_Model_Layout_Argument_Handler_Url')
        );
    }
}
