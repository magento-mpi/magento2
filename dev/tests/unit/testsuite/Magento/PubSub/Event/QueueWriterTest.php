<?php
/**
 * Magento_PubSub_Event_QueueWriter
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
        $event = $this->getMockBuilder('Magento_PubSub_EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $queue = new Magento_PubSub_Event_QueueWriter();
        $result = $queue->offer($event);

        $this->assertNull($result);
    }

}
