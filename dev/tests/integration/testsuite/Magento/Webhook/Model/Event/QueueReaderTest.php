<?php
/**
 * \Magento\Webhook\Model\Event\QueueReader
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_QueueReaderTest extends PHPUnit_Framework_TestCase
{
    public function testPoll()
    {
        /** @var \Magento\Webhook\Model\Event $event */
        $event = Mage::getModel('Magento\Webhook\Model\Event')
            ->setDataChanges(true)
            ->save();
        /** @var \Magento\Webhook\Model\Event\QueueReader $queue */
        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Event\QueueReader');
        $this->assertEquals($event->getId(), $queue->poll()->getId());

        // Make sure an empty queue returns null
        $this->assertNull($queue->poll());
    }
}
