<?php
/**
 * Mage_Webhook_Model_Webapi_EventHandler_Factory
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Webapi_EventHandler_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    /** @var Mage_Webhook_Model_Webapi_EventHandler_Factory */
    private $_factory;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new Mage_Webhook_Model_Webapi_EventHandler_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $mockEntity = $this->getMockBuilder('Mage_Webhook_Model_Webapi_EventHandler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Mage_Webhook_Model_Webapi_EventHandler'), $this->equalTo(array()))
            ->will($this->returnValue($mockEntity));
        $this->assertSame($mockEntity, $this->_factory->create());
    }
}
