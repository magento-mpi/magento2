<?php
/**
 * Magento_Webhook_Model_Resource_Subscription_Collection
 *
 * We need DB isolation to avoid confusing interactions with the other Webhook tests.
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Subscription_CollectionTest extends PHPUnit_Framework_TestCase
{
    const TOPIC_LISTENERS_THREE = 'listeners/three';
    const TOPIC_LISTENERS_TWO = 'listeners/two';
    const TOPIC_LISTENERS_ONE = 'listeners/one';
    const TOPIC_UNKNOWN = 'unknown';
    /**
     * API Key for user
     */
    const API_KEY = 'Magento_Webhook_Model_Resource_Subscription_CollectionTest';

    /** @var int */
    private static $_apiUserId;

    /** @var Magento_Webhook_Model_Resource_Subscription_Collection */
    private $_subscriptionSet;

    /** @var Magento_Webhook_Model_Subscription[]  */
    private $_subscriptions;

    public static function setUpBeforeClass()
    {
        /** @var Magento_Webapi_Model_Acl_User $user */
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webapi_Model_Acl_User');
        $user->loadByKey(self::API_KEY);
        if ($user->getId()) {
            self::$_apiUserId = $user->getId();
        } else {
            /** @var Magento_Webhook_Model_Webapi_User_Factory $webapiUserFactory */
            $webapiUserFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_Webhook_Model_Webapi_User_Factory');
            self::$_apiUserId = $webapiUserFactory->createUser(
                array(
                    'email'      => 'email@localhost.com',
                    'key'       => self::API_KEY,
                    'secret'    =>'secret'
                ),
                array()
            );
        }
    }

    protected function setUp()
    {
        $this->_subscriptions = array();

        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/one/label', 'One Listener');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/two/label', 'Two Listeners');
        Mage::getConfig()->setNode('global/webhook/webhooks/listeners/three/label', 'Three Listeners');

        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('inactive')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('Inactive Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_INACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('first')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/endpoint')
            ->setFormat('json')
            ->setName('First Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('second')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Second Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_TWO, self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();
        $this->_subscriptions[] = $subscription;

        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription');
        $subscription->setAlias('third')
            ->setAuthenticationType('hmac')
            ->setEndpointUrl('http://localhost/unique_endpoint')
            ->setFormat('json')
            ->setName('Third Subscription')
            ->setTopics(array(self::TOPIC_LISTENERS_ONE, self::TOPIC_LISTENERS_TWO, self::TOPIC_LISTENERS_THREE))
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->setApiUserId(self::$_apiUserId)
            ->save();
        $this->_subscriptions[] = $subscription;

        $this->_subscriptionSet = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Resource_Subscription_Collection');
    }

    protected function tearDown()
    {
        foreach ($this->_subscriptions as $subscription) {
            $subscription->delete();
        }
    }

    public function testGetSubscriptions()
    {
        $subscriptions   = $this->_subscriptionSet->getItems();
        $this->assertEquals(4, count($subscriptions));
    }

    public function testGetActiveSubscriptions()
    {
        $subscriptions   = $this->_subscriptionSet->addIsActiveFilter(true)->getItems();
        $this->assertEquals(3, count($subscriptions));
    }

    public function testGetInactiveSubscriptions()
    {
        $subscriptions   = $this->_subscriptionSet->addIsActiveFilter(false)->getItems();
        $this->assertEquals(1, count($subscriptions));
    }

    public function testGetUnknownTopicSubscriptions()
    {
        $subscriptions   = $this->_subscriptionSet->addTopicFilter(self::TOPIC_UNKNOWN)->getItems();
        $this->assertEquals(0, count($subscriptions));
    }

    public function testGetKnownTopicSubscriptions()
    {
        $subscriptions   = $this->_subscriptionSet->addTopicFilter(self::TOPIC_LISTENERS_ONE)->getItems();
        $this->assertEquals(1, count($subscriptions));
    }

    public function testGetSubscriptionsByTopic()
    {
        $subscriptions = $this->_subscriptionSet->getSubscriptionsByTopic(self::TOPIC_LISTENERS_THREE);

        $this->assertEquals(3, count($subscriptions));

        $subscriptions = $this->_subscriptionSet->getSubscriptionsByTopic(self::TOPIC_LISTENERS_TWO);

        $this->assertEquals(2, count($subscriptions));

        $subscriptions = $this->_subscriptionSet->getSubscriptionsByTopic(self::TOPIC_LISTENERS_ONE);

        $this->assertEquals(1, count($subscriptions));
    }

    public function testGetSubscriptionsByAlias()
    {
        $subscriptions = $this->_subscriptionSet->getSubscriptionsByAlias('first');
        // There should only be one item
        foreach ($subscriptions as $subscription) {
            $this->assertEquals('First Subscription', $subscription->getName());
        }
    }

    public function testGetActivatedSubscriptionsWithoutApiUser()
    {
        $subscriptions = $this->_subscriptionSet->getActivatedSubscriptionsWithoutApiUser();

        $this->assertEquals(2, count($subscriptions));
    }

    public function testGetApiUserSubscriptions()
    {
        $subscriptions = $this->_subscriptionSet->getApiUserSubscriptions(self::$_apiUserId);

        $this->assertEquals(1, count($subscriptions));
    }
}
