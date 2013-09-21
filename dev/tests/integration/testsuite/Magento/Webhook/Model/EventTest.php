<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model;

/**
 * \Magento\Webhook\Model\Event
 * @magentoDbIsolation enabled
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Webhook\Model\Event */
    private $_event;

    public function setUp()
    {
        $this->_event = \Mage::getModel('Magento\Webhook\Model\Event');
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

    public function testMarkAsProcessed()
    {
        $this->_event->complete();
        $this->assertEquals(\Magento\PubSub\EventInterface::STATUS_PROCESSED, $this->_event->getStatus());
    }

    public function testSaveAndLoad()
    {
        $bodyData = array('array', 'of', 'body', 'data');
        $eventId = $this->_event
            ->setBodyData($bodyData)
            ->save()
            ->getId();
        $loadedEvent = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Event')
            ->load($eventId);
        $this->assertEquals($bodyData, $loadedEvent->getBodyData());
    }
}
