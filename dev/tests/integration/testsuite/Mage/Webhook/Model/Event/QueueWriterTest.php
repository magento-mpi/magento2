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
    public function testOffer()
    {
        $bodyData = array('webhook', 'event', 'body', 'data');
        /** @var Mage_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueWriter');
        /** @var Mage_Webhook_Model_Event $event */
        $event = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->setBodyData($bodyData);
        $queueWriter->offer($event);
        /** @var Mage_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueReader');

        $this->assertEquals($event->getBodyData(), $queueReader->poll()->getBodyData());
    }

    public function testOfferOtherEvent()
    {
        $bodyData = array('webhook', 'event', 'body', 'data');
        $eventArgs = array('topic' => 'some topic', 'bodyData' => $bodyData);
        /** @var Mage_Webhook_Model_Event_QueueWriter $queueWriter */
        $queueWriter = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueWriter');
        /** @var Mage_Webhook_Model_Event_QueueReader $queueReader */
        $queueReader = Mage::getObjectManager()->create('Mage_Webhook_Model_Event_QueueReader');
        /** @var Magento_PubSub_Event $magentoEvent */
        $magentoEvent = Mage::getObjectManager()->create('Magento_PubSub_Event', $eventArgs);

        $queueWriter->offer($magentoEvent);
        $this->assertEquals($magentoEvent->getBodyData(), $queueReader->poll()->getBodyData());
    }
}