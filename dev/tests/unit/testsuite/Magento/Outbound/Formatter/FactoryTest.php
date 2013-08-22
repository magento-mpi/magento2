<?php
/**
 * Magento_Webhook_Model_Formatter_Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Formatter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject Magento_Core_Model_ObjectManager
     */
    private $_mockObjectManager;

    /**
     * @var Magento_Outbound_Formatter_Factory
     */
    protected $_formatterFactory;

    /**
     * @var Magento_Outbound_Formatter_Json
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_expectedObject = $this->getMockBuilder('Magento_Outbound_Formatter_Json')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_formatterFactory = new Magento_Outbound_Formatter_Factory(
            array('json' => 'Test_Formatter_Json'),
            $this->_mockObjectManager
        );
    }

    public function testGetFormatter()
    {
        $this->_mockObjectManager->expects($this->once())
            ->method('get')
            ->with('Test_Formatter_Json')
            ->will($this->returnValue($this->_expectedObject));

        $formatter = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento_Outbound_Formatter_Json', $formatter);
        $this->assertEquals($this->_expectedObject, $formatter);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage WrongFormatName
     */
    public function testGetFormatterWrongFormatName()
    {
        $this->_formatterFactory->getFormatter('WrongFormatName');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Formatter class for json does not implement FormatterInterface.
     */
    public function testGetFormatterWrongFormatterClass()
    {
        $this->_mockObjectManager->expects($this->once())
            ->method('get')
            ->with('Test_Formatter_Json')
            ->will($this->returnValue($this->getMock('Magento_Object')));

        $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
    }
}
