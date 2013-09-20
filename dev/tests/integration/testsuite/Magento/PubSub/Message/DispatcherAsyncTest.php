<?php
/**
 * \Magento\PubSub\Message\DispatcherAsync
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @magentoDbIsolation enabled
 */
namespace Magento\PubSub\Message;

class DispatcherAsyncTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PubSub\Message\DispatcherAsync
     */
    protected $_model;

    /**
     * Initialize the model
     */
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

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PubSub\Message\DispatcherAsync');
    }

    /**
     * Test the maing flow of event dispatching
     */
    public function testDispatch()
    {
        $topic = 'webhooks/dispatch/tested';

        $data = array(
            'testKey' => 'testValue'
        );

        $this->_model->dispatch($topic, $data);

        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\PubSub\Event\QueueReaderInterface');
        $event = $queue->poll();

        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }
}
