<?php
/**
 * Magento_PubSub_Message_DispatcherAsync
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
     * @var Magento_PubSub_Message_DispatcherAsync
     */
    protected $_model;

    /**
     * Initialize the model
     */
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

        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_PubSub_Message_DispatcherAsync');
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

        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_PubSub_Event_QueueReaderInterface');
        $event = $queue->poll();

        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals($data, $event->getBodyData());
    }
}
