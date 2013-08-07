<?php
/**
 * Mage_Webhook_Model_Event_QueueWriter
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    public function testOfferWebhookEvent()
    {
        // New collection must be createdto avoid interference between QueueReader tests
        $collection =  Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('webhook', 'event', 'body', 'data');
        /** @var Mage_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueWriter');
        /** @var Mage_Webhook_Model_Event $event */
        $event = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->setBodyData($bodyData);
        $queueWriter->offer($event);
        /** @var Mage_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueReader', $readerArgs);

        $this->assertEquals($event->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }

    public function testOfferMagentoEvent()
    {
        // New collection must be createdto avoid interference between QueueReader tests
        $collection =  Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('magento', 'event', 'body', 'data');
        /** @var Mage_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueWriter');
        /** @var Mage_Webhook_Model_Event $magentoEvent */
        $magentoEvent = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->setBodyData($bodyData);
        $queueWriter->offer($magentoEvent);
        /** @var Mage_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueReader', $readerArgs);

        $this->assertEquals($magentoEvent->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }
}