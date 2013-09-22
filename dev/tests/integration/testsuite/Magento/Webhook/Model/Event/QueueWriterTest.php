<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Event;

/**
 * \Magento\Webhook\Model\Event\QueueWriter
 *
 * @magentoDbIsolation enabled
 */
class QueueWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testOfferWebhookEvent()
    {
        $this->markTestSkipped("MAGETWO-11929 suite interaction issue.");
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
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
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
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
