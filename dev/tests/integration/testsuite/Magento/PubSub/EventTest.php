<?php
/**
 * \Magento\PubSub\Event
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';

        $event = new \Magento\PubSub\Event($topic, $bodyData);

        $this->assertEquals(array(), $event->getHeaders());
        $this->assertEquals($bodyData, $event->getBodyData());
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals(\Magento\PubSub\Event::STATUS_READY_TO_SEND, $event->getStatus());
    }

    public function testMarkProcessed()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';
        $event = new \Magento\PubSub\Event($topic, $bodyData);

        $event->complete();

        $this->assertEquals(\Magento\PubSub\Event::STATUS_PROCESSED, $event->getStatus());
    }
}
