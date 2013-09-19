<?php
/**
 * \Magento\Webhook\Model\Job\Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Job;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Job\Factory */
    private $_jobFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_jobFactory = new \Magento\Webhook\Model\Job\Factory($this->_mockObjectManager);
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
                $this->equalTo('Magento\Webhook\Model\Job'),
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
