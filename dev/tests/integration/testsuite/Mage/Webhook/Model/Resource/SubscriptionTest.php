<?php
/**
 * Mage_Webhook_Model_Resource_Subscription
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_SubscriptionTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Webhook_Model_Resource_Subscription */
    private $_resource;

    public function setUp()
    {
        $this->_resource = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Subscription');
    }

    public function testLoadTopics()
    {
        $topics = array(
            'customer/created',
            'customer/updated',
            'customer/deleted',
        );

        /** @var Mage_Webhook_Model_Subscription $subscription */
        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscription->setTopics($topics);
        $subscription->save();


        $this->_resource->loadTopics($subscription);
        // When topics are not set, getTopics() calls resource's loadTopics method
        $this->assertEquals($topics, $subscription->getTopics());
        $subscription->delete();
    }

    public function testSaveAndLoad()
    {
        $topics = array(
            'customer/created',
            'customer/updated',
            'customer/deleted',
        );

        $subscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $subscriptionId = $subscription
            ->setTopics($topics)
            ->setName('subscription to load')
            ->save()
            ->getId();

        // This is done so all of the topic save logic is used
        $topics[] = 'order/created';
        unset($topics[0]);
        $topics = array_values($topics); // Fix integer indices
        $subscription->setTopics($topics)
            ->save();

        $loadedSubscription = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription');
        $loadedSubscription->load($subscriptionId);

        $this->assertEquals('subscription to load', $loadedSubscription->getName());
        $this->assertEquals($topics, $loadedSubscription->getTopics());

    }
}