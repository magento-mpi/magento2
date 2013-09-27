<?php
/**
 * Magento_Webhook_Model_Job_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Job_Factory');
        $event = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription')
            ->setDataChanges(true)
            ->save();
        $job = $factory->create($subscription, $event);

        $this->assertInstanceOf('Magento_Webhook_Model_Job', $job);
        $this->assertEquals($event->getId(), $job->getEventId());
        $this->assertEquals($subscription->getId(), $job->getSubscriptionId());
    }
}
