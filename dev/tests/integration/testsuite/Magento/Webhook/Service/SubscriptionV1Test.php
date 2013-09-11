<?php
/**
 * \Magento\Webhook\Service\SubscriptionV1
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Service_SubscriptionV1Test extends PHPUnit_Framework_TestCase
{
    /** Constants for validation of field data */
    const VALUE_NAME = 'Name of the Subscriber';
    const VALUE_ALIAS = 'test alias';
    const VALUE_ENDPOINT_URL = 'http://localhost/reach_us_here';

    const KEY_ENDPOINT_URL = Magento_Webhook_Model_SubscriptionTest::KEY_ENDPOINT_URL;
    const KEY_NAME = Magento_Webhook_Model_SubscriptionTest::KEY_NAME;
    const KEY_ALIAS = Magento_Webhook_Model_SubscriptionTest::KEY_ALIAS;
    const KEY_API_USER_ID = Magento_Webhook_Model_SubscriptionTest::KEY_API_USER_ID;
    const KEY_TOPICS = 'topics';
    const KEY_STATUS = Magento_Webhook_Model_SubscriptionTest::KEY_STATUS;

    /** @var  array */
    private $_subscriptionData;

    /** @var  int */
    private $_apiUserId;

    public function setUp()
    {
        $userContext = array(
            'email'     => 'email@example.com',
            'key'       => 'key',
            'secret'    => 'secret',
        );
        /** @var \Magento\Webhook\Model\Webapi\User\Factory $webapiUserFactory */
        $webapiUserFactory = Mage::getModel('\Magento\Webhook\Model\Webapi\User\Factory');
        $this->_apiUserId = $webapiUserFactory->createUser($userContext, array('webhook/create'));

        $this->_subscriptionData = array(
            self::KEY_ALIAS => self::VALUE_ALIAS,
            self::KEY_NAME => self::VALUE_NAME,
            self::KEY_ENDPOINT_URL => self::VALUE_ENDPOINT_URL,
            self::KEY_API_USER_ID => $this->_apiUserId,
            // TODO: Right now if we check for topic permissions it will fail.  Not sure why.
            //self::KEY_TOPICS => array('webhook/create'),
        );
    }

    public function tearDown()
    {
        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = Mage::getModel('\Magento\Webapi\Model\Acl\User');
        $user->load($this->_apiUserId);
        $user->delete();
    }

    public function testCreate()
    {
        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->create($this->_subscriptionData);

        // verify
        $this->assertTrue($resultData['subscription_id'] > 0);
        $this->assertEquals(self::VALUE_ENDPOINT_URL, $resultData[self::KEY_ENDPOINT_URL]);
    }

    /**
     * No user exists yet, so we don't expect invalid topics to be identified at this point.
     */
    public function testCreateInvalidTopicsNoUser()
    {
        $this->_subscriptionData[self::KEY_TOPICS] = array('invalid/topic', 'also/invalid/topic');
        unset($this->_subscriptionData[self::KEY_API_USER_ID]);

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->create($this->_subscriptionData);

        $this->assertTrue($resultData['subscription_id'] > 0);
    }

    /**
     * @expectedException \Magento\Webhook\Exception
     * @expectedExceptionMessage not authorized
     */
    public function testCreateInvalidTopicsWithUser()
    {
        $this->_subscriptionData[self::KEY_TOPICS] = array('invalid/topic', 'also/invalid/topic');
        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->create($this->_subscriptionData);

        $this->assertTrue($resultData['subscription_id'] > 0);
    }

    public function testGet()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->save();

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->get($subscription->getId());

        $this->assertEquals($subscription->getId(), $resultData['subscription_id']);
        $this->assertEquals(self::VALUE_ENDPOINT_URL, $resultData[self::KEY_ENDPOINT_URL]);
    }

    /**
     * @expectedException \Magento\Webhook\Exception
     * @expectedExceptionMessage 0
     */
    public function testGetNotFound()
    {
        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $service->get(0);
    }

    public function testGetAll()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $first = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $first->setData($this->_subscriptionData);
        $first->save();

        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $second = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $second->setData($this->_subscriptionData);
        $second->save();

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $subscriptions = $service->getAll($this->_apiUserId);

        $this->assertEquals($first->getId(), $subscriptions[0]['subscription_id']);
        $this->assertEquals($second->getId(), $subscriptions[1]['subscription_id']);
    }

    public function testUpdate()
    {
        $newUrl = self::VALUE_ENDPOINT_URL . '/plus/this';
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->save();
        $subscriptionData = $subscription->getData();
        $subscriptionData[self::KEY_ENDPOINT_URL] = $newUrl;

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->update($subscriptionData);

        $this->assertEquals($subscription->getId(), $resultData['subscription_id']);
        $this->assertEquals($newUrl, $resultData[self::KEY_ENDPOINT_URL]);
        $this->assertEquals(self::VALUE_NAME, $resultData[self::KEY_NAME]);
    }

    public function testDelete()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->save();

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $resultData = $service->delete($subscription->getId());

        $this->assertEquals($subscription->getId(), $resultData['subscription_id']);
        $this->assertEquals(self::VALUE_ENDPOINT_URL, $resultData[self::KEY_ENDPOINT_URL]);
        $this->assertEquals(self::VALUE_NAME, $resultData[self::KEY_NAME]);


        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->load($resultData['subscription_id']);
        $this->assertEquals(0, $subscription->getId());
    }

    public function testActivate()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->save();
        // verify initial state
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE, $subscription->getStatus());

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $subscriptionData = $service->activate($subscription->getId());

        // verify change
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE, $subscriptionData[self::KEY_STATUS]);
        $subscription->load($subscription->getId());
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE, $subscription->getStatus());
    }

    public function testDeactivate()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE);
        $subscription->save();
        // verify initial state
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE, $subscription->getStatus());

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $subscriptionData = $service->deactivate($subscription->getId());

        // verify change
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE, $subscriptionData[self::KEY_STATUS]);
        $subscription->load($subscription->getId());
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE, $subscription->getStatus());
    }

    public function testRevoke()
    {
        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = Mage::getModel('\Magento\Webhook\Model\Subscription');
        $subscription->setData($this->_subscriptionData);
        $subscription->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE);
        $subscription->save();
        // verify initial state
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE, $subscription->getStatus());

        /** @var \Magento\Webhook\Service\SubscriptionV1 $service */
        $service = Mage::getModel('\Magento\Webhook\Service\SubscriptionV1');
        $subscriptionData = $service->revoke($subscription->getId());

        // verify change
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_REVOKED, $subscriptionData[self::KEY_STATUS]);
        $subscription->load($subscription->getId());
        $this->assertEquals(\Magento\Webhook\Model\Subscription::STATUS_REVOKED, $subscription->getStatus());
    }
}
