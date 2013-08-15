<?php
/**
 * Mage_Webhook_Model_Job_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Mage::getObjectManager()->create('Mage_Webhook_Model_Job_Factory');
        $event = Mage::getModel('Mage_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        $subscription = Mage::getModel('Mage_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();
        $job = $factory->create($subscription, $event);

        $this->assertInstanceOf('Mage_Webhook_Model_Job', $job);
        $this->assertEquals($event->getId(), $job->getEventId());
        $this->assertEquals($subscription->getId(), $job->getSubscriptionId());
    }
}