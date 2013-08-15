<?php
/**
 * Magento_Webhook_Model_Job_QueueWriter
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

    /** @var Magento_Webhook_Model_Job_QueueWriter */
    private $_jobQueue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_jobFactory;

    public function setUp()
    {
        $this->_jobFactory = $this->getMockBuilder('Magento_Webhook_Model_Job_Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $this->_jobQueue = new Magento_Webhook_Model_Job_QueueWriter($this->_jobFactory);
    }

    public function testOfferMagentoJob()
    {
        $magentoJob = $this->getMockBuilder('Magento_Webhook_Model_Job')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoJob->expects($this->once())
            ->method('save');
        $result = $this->_jobQueue->offer($magentoJob);
        $this->assertEquals(null, $result);
    }

    public function testOfferNonMagentoJob()
    {
        $magentoJob = $this->getMockBuilder('Magento_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoJob->expects($this->once())
            ->method('save');

        $this->_jobFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($magentoJob));


        $job = $this->getMockBuilder('Magento_PubSub_JobInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $subscription = $this->getMockBuilder('Magento_PubSub_SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder('Magento_PubSub_EventInterface')
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
