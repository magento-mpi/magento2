<?php
/**
 * \Magento\PubSub\Event\QueueWriter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

class QueueWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $event = $this->getMockBuilder('Magento\PubSub\EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $queue = new \Magento\PubSub\Event\QueueWriter();
        $result = $queue->offer($event);

        $this->assertNull($result);
    }

}
