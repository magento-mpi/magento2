<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Job;

/**
 * \Magento\Webhook\Model\Job\QueueWriter
 *
 * @magentoDbIsolation enabled
 */
class QueueWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testOffer()
    {
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

        /** @var \Magento\Webhook\Model\Event\QueueWriter $queueWriter */
        $queueWriter = $objectManager->create('Magento\Webhook\Model\Job\QueueWriter');
        $queueWriter->offer($job);

        /** @var \Magento\Webhook\Model\Event\QueueReader $queueReader */
        $queueReader = $objectManager->create('Magento\Webhook\Model\Job\QueueReader');

        $this->assertEquals($job->getId(), $queueReader->poll()->getId());
    }
}
