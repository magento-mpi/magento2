<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Job;

/**
 * \Magento\Webhook\Model\Job\QueueReader
 *
 * @magentoDbIsolation enabled
 */
class QueueReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testPoll()
    {
        $this->markTestSkipped("MAGETWO-11929 memory issue.");
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $event = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();

        $subscription = \Mage::getModel('Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save();

        /** @var \Magento\Webhook\Model\Job $job */
        $job = $objectManager->create('Magento\Webhook\Model\Job');
        $job->setEventId($event->getId());
        $job->setSubscriptionId($subscription->getId());

        $queueWriter = $objectManager->create('Magento\Webhook\Model\Job\QueueWriter');
        $queueWriter->offer($job);

        /** @var \Magento\Webhook\Model\Job\QueueReader $queueReader */
        $queueReader = $objectManager->create('Magento\Webhook\Model\Job\QueueReader');
        $this->assertEquals($job->getId(), $queueReader->poll()->getId());

        $this->assertNull($queueReader->poll());
    }
}
