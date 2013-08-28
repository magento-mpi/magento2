<?php
/**
 * Test Rest response controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Response_Rest */
    protected $_responseRest;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_Webapi_Controller_Response_Rest_Renderer_Xml */
    protected $_rendererMock;

    /** @var Magento_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessorMock;

    protected function setUp()
    {
        /** Mock all objects required for SUT. */
        $this->_rendererMock = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest_Renderer_Factory')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_rendererMock));
        $this->_errorProcessorMock = $this->getMockBuilder('Magento_Webapi_Controller_Dispatcher_ErrorProcessor')
            ->disableOriginalConstructor()->getMock();
        $this->_errorProcessorMock->expects($this->once())->method('maskException')->will($this->returnArgument(0));
        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();

        /** Init SUP. */
        $this->_responseRest = new Magento_Webapi_Controller_Response_Rest(
            $rendererFactoryMock,
            $this->_errorProcessorMock,
            $this->_appMock
        );
        $this->_responseRest->headersSentThrowsException = false;
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_responseRest);
        unset($this->_appMock);
        unset($this->_rendererMock);
        unset($this->_errorProcessorMock);
        parent::tearDown();
    }

    /**
     * Test setException method with Magento_Webapi_Exception.
     */
    public function testSetWebapiExceptionException()
    {
        /** Init Magento_Webapi_Exception */
        $apiException = new Magento_Webapi_Exception('Exception message.', 401);
        $this->_responseRest->setException($apiException);
        /** Assert that Magento_Webapi_Exception was set and presented in the list. */
        $this->assertTrue(
            $this->_responseRest->hasExceptionOfType('Magento_Webapi_Exception'),
            'Magento_Webapi_Exception was not set.'
        );
    }

    /**
     * Test sendResponse method with internal error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesException()
    {
        /** Init logic exception. */
        $logicException = new LogicException();
        /** Mock renderer to throw LogicException in getMimeType method. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            Magento_Webapi_Exception::HTTP_INTERNAL_ERROR
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new Magento_Webapi_Exception('Message.', 400));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with HTTP Not Acceptable error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesHttpNotAcceptable()
    {
        /** Init logic exception. */
        $logicException = new LogicException('Message', Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE);
        /** Mock renderer to throw LogicException in getMimeType method. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new Magento_Webapi_Exception('Message.', 400));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with exception rendering.
     *
     * @dataProvider dataProviderForSendResponseWithException
     */
    public function testSendResponseWithException($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );
        $this->_responseRest->setException($exception);
        /** Start output buffering. */
        ob_start();
        $this->_responseRest->sendResponse();
        /** Clear output buffering. */
        ob_end_clean();
        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    /**
     * Callback for testSendResponseRenderMessages method.
     *
     * @param $data
     * @return string
     */
    public function callbackForSendResponseTest($data)
    {
        return json_encode($data);
    }

    /**
     * Test sendResponse method with exception rendering.
     *
     * @dataProvider dataProviderForSendResponseWithExceptionInDeveloperMode
     */
    public function testSendResponseWithExceptionInDeveloperMode($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        $this->_responseRest->setException($exception);
        /** Start output buffering. */
        ob_start();
        $this->_responseRest->sendResponse();
        /** Clear output buffering. */
        ob_end_clean();
        $actualResponse = $this->_responseRest->getBody();
        $this->assertStringStartsWith($expectedResult, $actualResponse, $assertMessage);
    }

    /**
     * Data provider for testSendResponseWithException.
     *
     * @return array
     */
    public function dataProviderForSendResponseWithException()
    {
        return array(
            'Magento_Webapi_Exception' => array(
                new Magento_Webapi_Exception('Message', 400),
                '{"messages":{"error":[{"code":400,"message":"Message"}]}}',
                'Response sending with Magento_Webapi_Exception is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message', 100),
                '{"messages":{"error":[{"code":500,"message":"Message"}]}}',
                'Response sending with Logical Exception is invalid'
            ),
        );
    }

    /**
     * Data provider for testSendResponseWithExceptionInDeveloperMode.
     *
     * @return array
     */
    public function dataProviderForSendResponseWithExceptionInDeveloperMode()
    {
        return array(
            'Magento_Webapi_Exception' => array(
                new Magento_Webapi_Exception('Message', 400),
                '{"messages":{"error":[{"code":400,"message":"Message","trace":"',
                'Response sending with Magento_Webapi_Exception in developer mode is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message'),
                '{"messages":{"error":[{"code":500,"message":"Message","trace":"',
                'Response sending with Logical Exception in developer mode is invalid'
            ),
        );
    }
}
