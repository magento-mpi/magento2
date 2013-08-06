<?php
/**
 * Mage_Webhook_Model_Event
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDbIsolation enabled
 */
class Mage_Webhook_Model_EventTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Webhook_Model_Event */
    private $_event;

    public function setUp()
    {
        $this->_event = Mage::getModel('Mage_Webhook_Model_Event');
    }

    public function testSetGet()
    {
        $this->assertEmpty($this->_event->getBodyData());
        $data = array('body', 'data');
        $this->_event->setBodyData($data);
        $this->assertEquals($data, $this->_event->getBodyData());

        $this->assertEmpty($this->_event->getHeaders());
        $data = array('header', 'array');
        $this->_event->setHeaders($data);
        $this->assertEquals($data, $this->_event->getHeaders());
    }

    public function testSetGetArrays()
    {
        $this->_event->setStatus(42);
        $this->assertEquals(42, $this->_event->getStatus());

        $this->_event->setTopic('customer/topic');
        $this->assertEquals('customer/topic', $this->_event->getTopic());
    }

    public function testMarkAsReadyToSend()
    {
        $this->_event->markAsReadyToSend();
        $this->assertEquals(Magento_PubSub_EventInterface::READY_TO_SEND, $this->_event->getStatus());
    }

    public function testMarkAsProcessed()
    {
        $this->_event->markAsProcessed();
        $this->assertEquals(Magento_PubSub_EventInterface::PROCESSED, $this->_event->getStatus());
    }

    public function testSaveAndLoad()
    {
        $bodyData = array('array', 'of', 'body', 'data');
        $eventId = $this->_event
            ->setBodyData($bodyData)
            ->save()
            ->getId();
        $loadedEvent = Mage::getObjectManager()->create('Mage_Webhook_Model_Event')
            ->load($eventId);
        $this->assertEquals($bodyData, $loadedEvent->getBodyData());
    }
}