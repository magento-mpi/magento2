<?php
/**
 * \Magento\PubSub\Message\DispatcherAsync
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Message_DispatcherAsyncTest extends PHPUnit_Framework_TestCase
{
    /** @var  \Magento\PubSub\Message\DispatcherAsync */
    private $_dispatcher;

    /** PHPUnit_Framework_MockObject_MockObject */
    private $_eventFactoryMock;

    /** PHPUnit_Framework_MockObject_MockObject */
    private $_eventMock;

    /** @var  string[] Data that gets passed to event factory */
    private $_actualData = array();

    /** PHPUnit_Framework_MockObject_MockObject */
    private $_queueWriter;


    protected function setUp()
    {
        $this->_eventFactoryMock = $this->getMockBuilder('Magento\PubSub\Event\FactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_queueWriter = $this->getMockBuilder('Magento\PubSub\Event\QueueWriterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_queueWriter->expects($this->once())
            ->method('offer');

        // When the create method is called, program routes to the logEventData callback to log what arguments it
        // received.
        $this->_eventFactoryMock->expects($this->once())
            ->method('create')
            ->with()
            ->will($this->returnCallback(array($this, 'logEventData')));
        $this->_eventMock = $this->getMockBuilder('Magento\PubSub\EventInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_dispatcher = new \Magento\PubSub\Message\DispatcherAsync($this->_eventFactoryMock, $this->_queueWriter);
    }

    public function testDispatch()
    {
        $expectedData = array('topic' => 'event_topic', 'data' => 'event_data');
        $this->_dispatcher->dispatch($expectedData['topic'], $expectedData['data']);
        $this->assertEquals($expectedData, $this->_actualData);
    }

    /**
     * Log the topic and data that are passed to the event factory's create method. This is to ensure that the
     * create method is called as expected.
     *
     * @param $topic
     * @param $data
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function logEventData($topic, $data)
    {
        $this->_actualData = array('topic' => $topic, 'data' => $data);
        return $this->_eventMock;
    }
}
