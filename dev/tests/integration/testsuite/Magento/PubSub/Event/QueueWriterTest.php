<?php
/**
 * \Magento\PubSub\Event\QueueWriter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $event = new \Magento\PubSub\Event('topic', array());
        $queue = new \Magento\PubSub\Event\QueueWriter();
        $result = $queue->offer($event);

        $this->assertNull($result);
    }

}
