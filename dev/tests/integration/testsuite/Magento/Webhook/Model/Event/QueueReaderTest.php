<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Event;

/**
 * \Magento\Webhook\Model\Event\QueueReader
 *
 * @magentoDbIsolation enabled
 */
class QueueReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testPoll()
    {
        $this->markTestSkipped("MAGETWO-11929 suite interaction issue");
        /** @var \Magento\Webhook\Model\Event $event */
        $event = \Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();
        /** @var \Magento\Webhook\Model\Event\QueueReader $queue */
        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Event\QueueReader');
        $this->assertEquals($event->getId(), $queue->poll()->getId());

        // Make sure an empty queue returns null
        $this->assertNull($queue->poll());
    }
}
