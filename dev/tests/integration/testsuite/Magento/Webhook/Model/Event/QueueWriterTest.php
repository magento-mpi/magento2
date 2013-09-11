<?php
/**
 * \Magento\Webhook\Model\Event\QueueWriter
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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        // New collection must be created to avoid interference between QueueReader tests
        $collection =  $objectManager->create('Magento\Webhook\Model\Resource\Event\Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('webhook', 'event', 'body', 'data');
        /** @var \Magento\Webhook\Model\Event\QueueWriter $queueWriter */
        $queueWriter = $objectManager->create('Magento\Webhook\Model\Event\QueueWriter');
        /** @var \Magento\Webhook\Model\Event $event */
        $event = $objectManager->create('Magento\Webhook\Model\Event')
            ->setBodyData($bodyData);
        $queueWriter->offer($event);
        /** @var \Magento\Webhook\Model\Event\QueueReader $queueReader */
        $queueReader = $objectManager->create('Magento\Webhook\Model\Event\QueueReader', $readerArgs);

        $this->assertEquals($event->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }

    public function testOfferMagentoEvent()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        // New collection must be created to avoid interference between QueueReader tests
        $collection =  $objectManager->create('Magento\Webhook\Model\Resource\Event\Collection');
        $readerArgs = array('collection' => $collection);

        $bodyData = array('magento', 'event', 'body', 'data');
        $topic = 'some topic';
        $eventArgs = array(
            'bodyData' => $bodyData,
            'topic' => $topic
        );

        /** @var \Magento\Webhook\Model\Event\QueueWriter $queueWriter */
        $queueWriter = $objectManager->create('Magento\Webhook\Model\Event\QueueWriter');
        /** @var \Magento\Webhook\Model\Event $magentoEvent */
        $magentoEvent = $objectManager->create('Magento\PubSub\Event', $eventArgs);
        $queueWriter->offer($magentoEvent);
        /** @var \Magento\Webhook\Model\Event\QueueReader $queueReader */
        $queueReader = $objectManager->create('Magento\Webhook\Model\Event\QueueReader', $readerArgs);

        $this->assertEquals($magentoEvent->getBodyData(), $queueReader->poll()->getBodyData());
        // Make sure poll returns null after queue is empty
        $this->assertNull($queueReader->poll());
    }
}
