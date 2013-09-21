<?php
/**
 * \Magento\PubSub\Event\QueueHandler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

/**
 * @magentoDbIsolation enabled
 */
class QueueHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * mock endpoint url
     */
    const ENDPOINT_URL = 'http://localhost/';

    /**
     * @var \Magento\PubSub\Event\QueueHandler
     */
    protected $_model;

    public function setUp()
    {
        /** @var \Magento\Webhook\Model\Resource\Event\Collection $eventCollection */
        $eventCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Event\Collection');
        /** @var array $event */
        $events = $eventCollection->getItems();
        /** @var \Magento\Webhook\Model\Event $event */
        foreach ($events as $event) {
            $event->complete();
            $event->save();
        }

        /** @var $factory \Magento\Webhook\Model\Event\Factory */
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PubSub\Event\FactoryInterface');

        /** @var $event \Magento\Webhook\Model\Event */
        $factory->create('testinstance/created', array(
            'testKey1' => 'testValue1'
        ))->save();

        $factory->create('testinstance/updated', array(
            'testKey2' => 'testValue2'
        ))->save();

        $endpoint = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Endpoint')
            ->setEndpointUrl(self::ENDPOINT_URL)
            ->setFormat('json')
            ->setAuthenticationType('hmac')
            ->setTimeoutInSecs('20')
            ->save();

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(array(
            'Magento\Core\Model\Config\Base' => array(
                'parameters' => array(
                    'sourceData' => __DIR__ . '/../_files/config.xml',
                ),
            ),
            'Magento\Webhook\Model\Resource\Subscription' => array(
                'parameters' => array(
                    'config' => array('instance' => 'Magento\Core\Model\Config\Base'),
                ),
            )
        ));

        /** @var \Magento\Webhook\Model\Subscription $subscription */
        $subscription = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Subscription');
        $subscription->setData(
            array(
                'name' => 'test',
                'status' => \Magento\Webhook\Model\Subscription::STATUS_INACTIVE,
                'version' => 1,
                'alias' => 'test',
                'topics' => array(
                    'testinstance/created',
                    'testinstance/updated'
                ),
            ))->save();

        // Simulate activating of the subscription
        $webApiUser = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webapi\Model\Acl\User')
            ->setData('api_key', 'test')
            ->setData('secret', 'secret')
            ->save();
        $endpoint->setApiUserId($webApiUser->getId())
            ->save();
        $subscription->setEndpointId($endpoint->getId())
            ->setStatus(\Magento\Webhook\Model\Subscription::STATUS_ACTIVE)
            ->save();;

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\PubSub\Event\QueueHandler');
    }

    /**
     * Test the main flow of event queue handling
     */
    public function testHandle()
    {
        $this->_model->handle();
        /** @var $queue \Magento\PubSub\Job\QueueReaderInterface */
        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\PubSub\Job\QueueReaderInterface');

        /* First EVENT */
        $job = $queue->poll();
        $this->assertNotNull($job);
        $this->assertInstanceOf('Magento\PubSub\JobInterface', $job);
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
