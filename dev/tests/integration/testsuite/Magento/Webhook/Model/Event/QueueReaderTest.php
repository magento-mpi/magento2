<?php
/**
 * Magento_Webhook_Model_Event_QueueReader
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
        /** @var Magento_Webhook_Model_Event $event */
        $event = Mage::getModel('Magento_Webhook_Model_Event')
            ->setDataChanges(true)
            ->save();
        /** @var Magento_Webhook_Model_Event_QueueReader $queue */
        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Webhook_Model_Event_QueueReader');
        $this->assertEquals($event->getId(), $queue->poll()->getId());

        // Make sure an empty queue returns null
        $this->assertNull($queue->poll());
    }
}
