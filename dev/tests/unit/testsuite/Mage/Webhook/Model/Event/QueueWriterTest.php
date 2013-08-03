<?php
/**
 * Mage_Webhook_Model_Event_QueueWriter
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Event_QueueWriter */
    protected $_eventQueue;

    /** @var Mage_Webhook_Model_Event_Factory  */
    protected $_eventFactory;

    public function setUp()
    {
        $this->_eventFactory = $this->_mockCollection = $this->getMockBuilder('Mage_Webhook_Model_Event_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_eventQueue = new Mage_Webhook_Model_Event_QueueWriter($this->_eventFactory);
    }

    public function testOfferMagentoEvent()
    {
        $magentoEvent = $this->_mockCollection = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');
        $result = $this->_eventQueue->offer($magentoEvent);
        $this->assertEquals(null, $result);
    }

    public function testOfferNonMagentoEvent()
    {
        $magentoEvent = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');

        $this->_eventFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($magentoEvent));


        $event = $this->getMockBuilder('Magento_PubSub_EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->_eventQueue->offer($event);
        $this->assertEquals(null, $result);
    }
}
