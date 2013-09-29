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
namespace Magento\Outbound\Formatter;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject \Magento\Core\Model\ObjectManager
     */
    private $_mockObjectManager;

    /**
     * @var \Magento\Outbound\Formatter\Factory
     */
    protected $_formatterFactory;

    /**
     * @var \Magento\Outbound\Formatter\Json
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_expectedObject = $this->getMockBuilder('Magento\Outbound\Formatter\Json')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_formatterFactory = new \Magento\Outbound\Formatter\Factory(
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

        $formatter = $this->_formatterFactory->getFormatter(\Magento\Outbound\EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento\Outbound\Formatter\Json', $formatter);
        $this->assertEquals($this->_expectedObject, $formatter);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage WrongFormatName
     */
    public function testGetFormatterWrongFormatName()
    {
        $this->_formatterFactory->getFormatter('WrongFormatName');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Formatter class for json does not implement FormatterInterface.
     */
    public function testGetFormatterWrongFormatterClass()
    {
        $this->_mockObjectManager->expects($this->once())
            ->method('get')
            ->with('Test_Formatter_Json')
            ->will($this->returnValue($this->getMock('Magento\Object')));

        $this->_formatterFactory->getFormatter(\Magento\Outbound\EndpointInterface::FORMAT_JSON);
    }
}
