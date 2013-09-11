<?php
/**
 * \Magento\Webhook\Model\Event\QueueReader
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_QueueReaderTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Event\QueueReader */
    protected $_eventQueue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockCollection;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockIterator;

    public function setUp()
    {
        $this->_mockCollection = $this->getMockBuilder('Magento\Webhook\Model\Resource\Event\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockIterator = $this->getMockBuilder('Iterator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($this->_mockIterator));
        $this->_eventQueue = new \Magento\Webhook\Model\Event\QueueReader($this->_mockCollection);
    }

    public function testPollEvent()
    {
        $this->_mockIterator->expects($this->once())
            ->method('valid')
            ->will($this->returnValue(true));

        $event = $this->getMockBuilder('Magento\Webhook\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockIterator->expects($this->once())
            ->method('current')
            ->will($this->returnValue($event));

        $this->_mockIterator->expects($this->once())
            ->method('next');

        $this->assertSame($event, $this->_eventQueue->poll());
    }

    public function testPollNothing()
    {
        $this->_mockIterator->expects($this->once())
            ->method('valid')
            ->will($this->returnValue(false));

        $this->_mockIterator->expects($this->never())
            ->method('current');

        $this->_mockIterator->expects($this->never())
            ->method('next');

        $this->assertNull($this->_eventQueue->poll());
    }
}
