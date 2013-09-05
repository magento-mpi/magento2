<?php
/**
 * Magento_Webhook_Model_Job_Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webhook_Model_Job_Factory */
    private $_jobFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_jobFactory = new Magento_Webhook_Model_Job_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $subscription = $this->getMockBuilder('Magento\PubSub\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder('Magento\PubSub\EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $job = 'JOB';
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento_Webhook_Model_Job'),
                $this->equalTo(
                    array(
                         'data' => array(
                             'event'        => $event,
                             'subscription' => $subscription
                         )
                    )
                )
            )
            ->will($this->returnValue($job));
        $this->assertSame($job, $this->_jobFactory->create($subscription, $event));
    }
}
