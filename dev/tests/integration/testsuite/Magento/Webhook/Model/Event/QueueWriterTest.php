<?php
/**
 * Magento_Webhook_Model_Event_QueueWriter
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    public function testOfferWebhookEvent()
    {
        // New collection must be created to avoid interference between QueueReader tests
        $collection =  Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event_Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('webhook', 'event', 'body', 'data');
        /** @var Magento_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Magento_Webhook_Model_Event_QueueWriter');
        /** @var Magento_Webhook_Model_Event $event */
        $event = Mage::getObjectManager()->create('Magento_Webhook_Model_Event')
            ->setBodyData($bodyData);
        $queueWriter->offer($event);
        /** @var Magento_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Magento_Webhook_Model_Event_QueueReader', $readerArgs);

        $this->assertEquals($event->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }

    public function testOfferMagentoEvent()
    {
        // New collection must be created to avoid interference between QueueReader tests
        $collection =  Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event_Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('magento', 'event', 'body', 'data');
        $topic = 'some topic';
        $eventArgs = array(
            'bodyData' => $bodyData,
            'topic' => $topic
        );

        /** @var Magento_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Magento_Webhook_Model_Event_QueueWriter');
        /** @var Magento_Webhook_Model_Event $magentoEvent */
        $magentoEvent = Mage::getObjectManager()->create('Magento_PubSub_Event', $eventArgs);
        $queueWriter->offer($magentoEvent);
        /** @var Magento_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Magento_Webhook_Model_Event_QueueReader', $readerArgs);

        $this->assertEquals($magentoEvent->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }
}
