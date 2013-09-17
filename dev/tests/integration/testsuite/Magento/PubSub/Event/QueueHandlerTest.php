<?php
/**
 * Magento_PubSub_Event_QueueHandler
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Event_QueueHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * mock endpoint url
     */
    const ENDPOINT_URL = 'http://localhost/';

    /**
     * @var Magento_PubSub_Event_QueueHandler
     */
    protected $_model;

    protected function setUp()
    {
        /** @var Magento_Webhook_Model_Resource_Event_Collection $eventCollection */
        $eventCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Resource_Event_Collection');
        /** @var array $event */
        $events = $eventCollection->getItems();
        /** @var Magento_Webhook_Model_Event $event */
        foreach ($events as $event) {
            $event->complete();
            $event->save();
        }

        /** @var $factory Magento_Webhook_Model_Event_Factory */
        $factory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_PubSub_Event_FactoryInterface');

        /** @var $event Magento_Webhook_Model_Event */
        $factory->create('testinstance/created', array(
            'testKey1' => 'testValue1'
        ))->save();

        $factory->create('testinstance/updated', array(
            'testKey2' => 'testValue2'
        ))->save();

        $endpoint = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Endpoint')
            ->setEndpointUrl(self::ENDPOINT_URL)
            ->setFormat('json')
            ->setAuthenticationType('hmac')
            ->setTimeoutInSecs('20')
            ->save();

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->configure(array(
            'Magento_Core_Model_Config_Base' => array(
                'parameters' => array(
                    'sourceData' => __DIR__ . '/../_files/config.xml',
                ),
            ),
            'Magento_Webhook_Model_Resource_Subscription' => array(
                'parameters' => array(
                    'config' => array('instance' => 'Magento_Core_Model_Config_Base'),
                ),
            )
        ));

        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Subscription');
        $subscription->setData(
            array(
                'name' => 'test',
                'status' => Magento_Webhook_Model_Subscription::STATUS_INACTIVE,
                'version' => 1,
                'alias' => 'test',
                'topics' => array(
                    'testinstance/created',
                    'testinstance/updated'
                ),
            ))->save();

        // Simulate activating of the subscription
        $webApiUser = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_User')
            ->setData('api_key', 'test')
            ->setData('secret', 'secret')
            ->save();
        $endpoint->setApiUserId($webApiUser->getId())
            ->save();
        $subscription->setEndpointId($endpoint->getId())
            ->setStatus(Magento_Webhook_Model_Subscription::STATUS_ACTIVE)
            ->save();;

        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_PubSub_Event_QueueHandler');
    }

    /**
     * Test the main flow of event queue handling
     */
    public function testHandle()
    {
        $this->_model->handle();
        /** @var $queue Magento_PubSub_Job_QueueReaderInterface */
        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_PubSub_Job_QueueReaderInterface');

        /* First EVENT */
        $job = $queue->poll();
        $this->assertNotNull($job);
        $this->assertInstanceOf('Magento_PubSub_JobInterface', $job);
        $event = $job->getEvent();
        $subscription = $job->getSubscription();

        $this->assertEquals('testinstance/created', $event->getTopic());
        $this->assertEquals(array('testKey1' => 'testValue1'), $event->getBodyData());

        $this->assertEquals(self::ENDPOINT_URL, $subscription->getEndpointUrl());
        $this->assertEquals(20, $subscription->getTimeoutInSecs());

        /* Second EVENT */
        $job = $queue->poll();
        $this->assertNotNull($job);
        $event = $job->getEvent();
        $subscription = $job->getSubscription();

        $this->assertEquals('testinstance/updated', $event->getTopic());
        $this->assertEquals(array('testKey2' => 'testValue2'), $event->getBodyData());

        $this->assertEquals(self::ENDPOINT_URL, $subscription->getEndpointUrl());
        $this->assertEquals(20, $subscription->getTimeoutInSecs());

        /* No more EVENTS */
        $job = $queue->poll();
        $this->assertNull($job);
    }
}
