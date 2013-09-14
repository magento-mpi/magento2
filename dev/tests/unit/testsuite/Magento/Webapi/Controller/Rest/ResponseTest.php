<?php
/**
 * Test Rest response controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_ResponseTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Rest_Response */
    protected $_responseRest;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_Webapi_Controller_Rest_Response_Renderer_Xml */
    protected $_rendererMock;

    /** @var Magento_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessorMock;

    protected function setUp()
    {
        /** Mock all objects required for SUT. */
        $this->_rendererMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Response_Renderer_Json')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Response_Renderer_Factory')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_rendererMock));
        $this->_errorProcessorMock = $this->getMockBuilder('Magento_Webapi_Controller_ErrorProcessor')
            ->disableOriginalConstructor()->getMock();
        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();

        /** Init SUP. */
        $this->_responseRest = new Magento_Webapi_Controller_Rest_Response(
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
        $apiException = new Magento_Webapi_Exception('Exception message.', 0,
            Magento_Webapi_Exception::HTTP_UNAUTHORIZED);
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
        /** Mock error processor to throw LogicException in maskException method. */
        $this->_errorProcessorMock->expects($this->any())->method('maskException')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            Magento_Webapi_Exception::HTTP_INTERNAL_ERROR
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(
            new Magento_Webapi_Exception('Message.'));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with HTTP Not Acceptable error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesHttpNotAcceptable()
    {
        $exception = new Magento_Webapi_Exception('Message', 0, Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE);
        /** Mock error processor to throw LogicException in maskException method. */
        $this->_errorProcessorMock->expects($this->any())->method('maskException')->will(
            $this->throwException($exception)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $exception,
            Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(
            new Magento_Webapi_Exception('Message.', 0, Magento_Webapi_Exception::HTTP_BAD_REQUEST));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with exception rendering.
     */
    public function testSendResponseWithException()
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );
        $exceptionMessage = 'Message';
        $exceptionHttpCode = Magento_Webapi_Exception::HTTP_BAD_REQUEST;
        $exception = new Magento_Webapi_Exception($exceptionMessage, 0, $exceptionHttpCode);
        $this->_errorProcessorMock->expects($this->any())->method('maskException')->will(
            $this->returnValue($exception)
        );
        $this->_responseRest->setException($exception);
        /** Start output buffering. */
        ob_start();
        $this->_responseRest->sendResponse();
        /** Clear output buffering. */
        ob_end_clean();
        $actualResponse = $this->_responseRest->getBody();
        $expectedResult = '{"errors":[{"message":"' . $exceptionMessage . '","http_code":' . $exceptionHttpCode . '}]}';
        $this->assertStringStartsWith($expectedResult, $actualResponse, 'Response body is invalid');
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
     * Test sendResponse method without any exception
     */
    public function testSendResponseSuccessHandling()
    {
        $this->_responseRest->sendResponse();
        $this->assertTrue(
            $this->_responseRest->getHttpResponseCode() == Magento_Webapi_Controller_Response::HTTP_OK
        );
    }
}
