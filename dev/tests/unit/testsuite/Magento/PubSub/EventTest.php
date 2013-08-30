<?php
/**
 * Magento_PubSub_Event
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_EventTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';

        $event = new Magento_PubSub_Event($topic, $bodyData);

        $this->assertEquals(array(), $event->getHeaders());
        $this->assertEquals($bodyData, $event->getBodyData());
        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals(Magento_PubSub_Event::STATUS_READY_TO_SEND, $event->getStatus());
    }

    public function testComplete()
    {
        $bodyData = array('some' => 'body');
        $topic = 'topic';
        $event = new Magento_PubSub_Event($topic, $bodyData);

        $event->complete();

        $this->assertEquals(Magento_PubSub_Event::STATUS_PROCESSED, $event->getStatus());
    }
}
