<?php
/**
 * \Magento\Webhook\Model\Job\Factory
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
            ->create('Magento\Webhook\Model\Job\Factory');
        $event = Mage::getModel('\Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription')
            ->setDataChanges(true)
            ->save();
        $job = $factory->create($subscription, $event);

        $this->assertInstanceOf('\Magento\Webhook\Model\Job', $job);
        $this->assertEquals($event->getId(), $job->getEventId());
        $this->assertEquals($subscription->getId(), $job->getSubscriptionId());
    }
}
