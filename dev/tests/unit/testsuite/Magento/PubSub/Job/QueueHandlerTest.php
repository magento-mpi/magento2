<?php
/**
 * Magento_PubSub_Job_QueueHandler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Job_QueueHandlerTest extends PHPUnit_Framework_TestCase
{

    /** @var  Magento_PubSub_Job_QueueHandler */
    private $_queueHandler;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_eventMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_eventMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_queueReaderMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_queueWriterMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_msgFactoryMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_transportMock;

    public function setUp()
    {
        // Object mocks
        $this->_subscriptionMockA = $this->getMockBuilder('Mage_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_subscriptionMockB = clone $this->_subscriptionMockA;

        $this->_eventMockA = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_eventMockB = clone $this->_eventMockA;

        $this->_msgFactoryMock = $this->getMockBuilder('Magento_Outbound_Message_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_transportMock = $this->getMockBuilder('Magento_Outbound_Transport_Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_queueReaderMock = $this->getMockBuilder('Mage_Webhook_Model_Job_QueueReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_queueWriterMock = $this->getMockBuilder('Mage_Webhook_Model_Job_QueueWriter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_messageMockA = $this->getMockBuilder('Magento_Outbound_Message')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_messageMockB = clone $this->_messageMockA;

    }

    public function testHandle()
    {
        $endpointA = $this->getMockBuilder('Magento_Outbound_EndpointInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionMockA->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue($endpointA));

        $endpointB = $this->getMockBuilder('Magento_Outbound_EndpointInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionMockB->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue($endpointB));

        // Resources for stubs
        $jobMsgMap = array(
            array($endpointA, $this->_eventMockA, $this->_messageMockA),
            array($endpointB, $this->_eventMockB, $this->_messageMockB),
        );

        $responseA = $this->getMockBuilder('Magento_Outbound_Transport_Http_Response')
            ->disableOriginalConstructor()
            ->getMock();
        $responseA->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));
        $responseB = $this->getMockBuilder('Magento_Outbound_Transport_Http_Response')
            ->disableOriginalConstructor()
            ->getMock();
        $responseB->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $msgResponseMap = array(
            array($this->_messageMockA, $responseA),
            array($this->_messageMockB, $responseB),
        );


        // Message factory create
        $this->_msgFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap($jobMsgMap));

        // Transport dispatch
        $this->_transportMock->expects($this->exactly(2))
            ->method('dispatch')
            ->will($this->returnValueMap($msgResponseMap));

        // Job stubs
        $jobMockA = $this->getMockBuilder('Mage_Webhook_Model_Job')
            ->disableOriginalConstructor()
            ->getMock();

        $jobMockB = $this->getMockBuilder('Mage_Webhook_Model_Job')
            ->disableOriginalConstructor()
            ->getMock();

        $jobMockA->expects($this->once())
            ->method('complete');

        $jobMockB->expects($this->once())
            ->method('handleFailure');

        $jobMockA->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockA));

        $jobMockB->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockB));

        $jobMockA->expects($this->once())
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockA));

        $jobMockB->expects($this->once())
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockB));

        // Queue contains two jobs, and will then return null to stop the loop
        $this->_queueReaderMock->expects($this->exactly(3))
            ->method('poll')
            ->with()
            ->will($this->onConsecutiveCalls(
                $jobMockA,
                $jobMockB,
                null
            ));

        $this->_queueHandler = new Magento_PubSub_Job_QueueHandler(
            $this->_queueReaderMock,
            $this->_queueWriterMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
    }

    public function testHandleEmptyQueue()
    {
        $this->_expectedCodes = array ();

        // Queue contains no jobs
        $this->_queueReaderMock->expects($this->once())
            ->method('poll')
            ->with()
            ->will($this->onConsecutiveCalls(
                null
            ));

        // Message factory create should never  be called
        $this->_msgFactoryMock->expects($this->never())
            ->method('create');

        // Transport dispatch should never be called
        $this->_transportMock->expects($this->never())
            ->method('dispatch');

        $this->_queueHandler = new Magento_PubSub_Job_QueueHandler(
            $this->_queueReaderMock,
            $this->_queueWriterMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
    }
}