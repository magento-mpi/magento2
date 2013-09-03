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
class Magento_PubSub_Message_DispatcherAsyncTest extends PHPUnit_Framework_TestCase
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
        /** @var Magento_Webhook_Model_Resource_Event_Collection $eventCollection */
        $eventCollection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event_Collection');
        /** @var array $event */
        $events = $eventCollection->getItems();
        /** @var Magento_Webhook_Model_Event $event */
        foreach ($events as $event) {
            $event->complete();
            $event->save();
        }

        $this->_model = Mage::getObjectManager()->create('Magento\PubSub\Message\DispatcherAsync');
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

        $queue = Mage::getObjectManager()->get('Magento\PubSub\Event\QueueReaderInterface');
        $event = $queue->poll();

        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }
}
