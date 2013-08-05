<?php
/**
 * Mage_Webhook_Model_Job_QueueReader
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Job_QueueReaderTest extends PHPUnit_Framework_TestCase
{
    public function testPoll()
    {
        $event = Mage::getModel('Mage_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();

        $subscription = Mage::getModel('Mage_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();

        /** @var Mage_Webhook_Model_Job $job */
        $job = Mage::getObjectManager()->create('Mage_Webhook_Model_Job');
        $job->setEventId($event->getId());
        $job->setSubscriptionId($subscription->getId());
        $job->save();

        /** @var Mage_Webhook_Model_Job_QueueReader $queue */
        $queue = Mage::getObjectManager()->create('Mage_Webhook_Model_Job_QueueReader');
        $this->assertEquals($job->getId(), $queue->poll()->getId());
    }
}