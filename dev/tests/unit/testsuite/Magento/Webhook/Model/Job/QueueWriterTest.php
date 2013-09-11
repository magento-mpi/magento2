<?php
/**
 * \Magento\Webhook\Model\Job\QueueWriter
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_QueueWriterTest extends PHPUnit_Framework_TestCase
{

    /** @var \Magento\Webhook\Model\Job\QueueWriter */
    private $_jobQueue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_jobFactory;

    public function setUp()
    {
        $this->_jobFactory = $this->getMockBuilder('Magento\Webhook\Model\Job\Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $this->_jobQueue = new \Magento\Webhook\Model\Job\QueueWriter($this->_jobFactory);
    }

    public function testOfferMagentoJob()
    {
        $magentoJob = $this->getMockBuilder('Magento\Webhook\Model\Job')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoJob->expects($this->once())
            ->method('save');
        $result = $this->_jobQueue->offer($magentoJob);
        $this->assertEquals(null, $result);
    }

    public function testOfferNonMagentoJob()
    {
        $magentoJob = $this->getMockBuilder('Magento\Webhook\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoJob->expects($this->once())
            ->method('save');

        $this->_jobFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($magentoJob));


        $job = $this->getMockBuilder('Magento\PubSub\JobInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $subscription = $this->getMockBuilder('Magento\PubSub\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder('Magento\PubSub\EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $job->expects($this->once())
            ->method('getSubscription')
            ->will($this->returnValue($subscription));
        $job->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($event));
        $result = $this->_jobQueue->offer($job);
        $this->assertEquals(null, $result);
    }
}
