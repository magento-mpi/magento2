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
    private $_jobMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_jobMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_queueMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockA;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_messageMockB;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_msgFactoryMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_transportMock;

    /** @var  int[] Expected response codes */
    private $_responseCodes = array();

    /** @var  int[] Actual responce codes */
    private $_expectedCodes;

    public function setUp()
    {
        // Object mocks
        $this->_subscriptionMockA = $this->getMockBuilder('Magento_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionMockB = clone $this->_subscriptionMockA;

        $this->_eventMockA = $this->getMockBuilder('Magento_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_eventMockB = clone $this->_eventMockA;

        $this->_jobMockA = $this->getMockBuilder('Magento_Webhook_Model_Job')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_msgFactoryMock = $this->getMockBuilder('Magento_Outbound_Message_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_transportMock = $this->getMockBuilder('Magento_Outbound_Transport_Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_queueMock = $this->getMockBuilder('Magento_Webhook_Model_Job_QueueReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_messageMockA = $this->getMockBuilder('Magento_Outbound_Message')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_messageMockB = clone $this->_messageMockA;

    }

    public function testHandle()
    {
        // Resources for stubs
        $jobMsgMap = array(
            array($this->_subscriptionMockA, $this->_eventMockA, $this->_messageMockA),
            array($this->_subscriptionMockB, $this->_eventMockB, $this->_messageMockB),
        );

        $responseA = new Magento_Outbound_Transport_Http_Response(new Zend_Http_Response(200, array()));
        $responseB = new Magento_Outbound_Transport_Http_Response(new Zend_Http_Response(404, array()));

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
        $this->_jobMockA->expects($this->exactly(2))
            ->method('handleResponse')
            ->will($this->returnCallback(array($this, 'logCode')));
        $this->_jobMockB = clone $this->_jobMockA;

        $this->_jobMockA->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockA));

        $this->_jobMockB->expects($this->once())
            ->method('getSubscription')
            ->with()
            ->will($this->returnValue($this->_subscriptionMockB));

        $this->_jobMockA->expects($this->once())
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockA));

        $this->_jobMockB->expects($this->exactly(2))
            ->method('getEvent')
            ->with()
            ->will($this->returnValue($this->_eventMockB));

        $this->_expectedCodes = array (200, 404);

        // Queue contains two jobs, and will then return null to stop the loop
        $this->_queueMock->expects($this->exactly(3))
            ->method('poll')
            ->with()
            ->will($this->onConsecutiveCalls(
                $this->_jobMockA,
                $this->_jobMockB,
                null
            ));

        $this->_queueHandler = new Magento_PubSub_Job_QueueHandler(
            $this->_queueMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
        $this->assertEquals($this->_expectedCodes, $this->_responseCodes);
    }

    public function testHandleEmptyQueue()
    {
        $this->_expectedCodes = array ();

        // Queue contains no jobs
        $this->_queueMock->expects($this->once())
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
            $this->_queueMock,
            $this->_transportMock,
            $this->_msgFactoryMock
        );

        $this->_queueHandler->handle();
        $this->assertEquals($this->_expectedCodes, $this->_responseCodes);
    }

    /**
     * Supplied as a callback function, receives the input to handleResponse and logs response codes
     *
     * @param Magento_Outbound_Transport_Http_Response $response
     */
    public function logCode(Magento_Outbound_Transport_Http_Response $response)
    {
        $this->_responseCodes[] = $response->getStatusCode();
    }
}