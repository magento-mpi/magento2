<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

/**
 * \Magento\Webhook\Model\Resource\Subscription
 *
 * @magentoDbIsolation enabled
 */
class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Webhook\Model\Resource\Subscription */
    private $_resource;

    public function setUp()
    {
        $this->_resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Subscription');
    }

    public function testLoadTopics()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $topics = array(
            'customer/created',
            'customer/updated',
            'customer/deleted',
        );

        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Subscription');
        $subscription->setTopics($topics);
        $subscription->save();


        $this->_resource->loadTopics($subscription);
        // When topics are not set, getTopics() calls resource's loadTopics method
        $this->assertEquals($topics, $subscription->getTopics());
        $subscription->delete();
    }

    public function testSaveAndLoad()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        $topics = array(
            'customer/created',
            'customer/updated',
            'customer/deleted',
        );

        $subscription = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Subscription');
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

        $loadedSubscription = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Subscription');
        $loadedSubscription->load($subscriptionId);

        $this->assertEquals('subscription to load', $loadedSubscription->getName());
        $this->assertEquals($topics, $loadedSubscription->getTopics());

    }
}
