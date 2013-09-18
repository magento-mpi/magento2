<?php
/**
 * \Magento\Webhook\Model\Event\QueueWriter
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Event\QueueWriter */
    protected $_eventQueue;

    /** @var \Magento\Webhook\Model\Event\Factory  */
    protected $_eventFactory;

    protected function setUp()
    {
        $this->_eventFactory = $this->_mockCollection = $this->getMockBuilder('Magento\Webhook\Model\Event\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_eventQueue = new \Magento\Webhook\Model\Event\QueueWriter($this->_eventFactory);
    }

    public function testOfferMagentoEvent()
    {
        $magentoEvent = $this->_mockCollection = $this->getMockBuilder('Magento\Webhook\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');
        $result = $this->_eventQueue->offer($magentoEvent);
        $this->assertEquals(null, $result);
    }

    public function testOfferNonMagentoEvent()
    {
        $magentoEvent = $this->getMockBuilder('Magento\Webhook\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');

        $this->_eventFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($magentoEvent));


        $event = $this->getMockBuilder('Magento\PubSub\EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->_eventQueue->offer($event);
        $this->assertEquals(null, $result);
    }
}
