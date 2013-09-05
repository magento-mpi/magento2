<?php
/**
 * Magento_Webhook_Model_Subscription_Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    /** @var Magento_Webhook_Model_Subscription_Factory */
    private $_factory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockSubscription;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockSubscription = $this->getMockBuilder('Magento_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new Magento_Webhook_Model_Subscription_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $mockSubscription = $this->getMockBuilder('Magento_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();
        $dataArray = array('test' => 'data');
        $mockSubscription->expects($this->once())
            ->method('setData')
            ->with($dataArray);
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Webhook_Model_Subscription'), $this->equalTo(array()))
            ->will($this->returnValue($mockSubscription));
        $this->assertSame($mockSubscription, $this->_factory->create($dataArray));
    }
}
