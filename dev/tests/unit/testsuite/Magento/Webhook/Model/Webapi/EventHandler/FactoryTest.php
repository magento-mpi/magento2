<?php
/**
 * Magento_Webhook_Model_Webapi_EventHandler_Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_EventHandler_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    /** @var Magento_Webhook_Model_Webapi_EventHandler_Factory */
    private $_factory;

    protected function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new Magento_Webhook_Model_Webapi_EventHandler_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $mockEntity = $this->getMockBuilder('Magento_Webhook_Model_Webapi_EventHandler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Webhook_Model_Webapi_EventHandler'), $this->equalTo(array()))
            ->will($this->returnValue($mockEntity));
        $this->assertSame($mockEntity, $this->_factory->create());
    }
}
