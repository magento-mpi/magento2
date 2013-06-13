<?php
/**
 * Test Rest response controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Response_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_responseRest;

    /** @var Mage_Core_Model_App */
    protected $_appMock;

    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Xml */
    protected $_rendererMock;

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessorMock;

    protected function setUp()
    {
        /** Mock all objects required for SUT. */
        $this->_rendererMock = $this->getMockBuilder('Mage_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock = $this->getMockBuilder('Mage_Webapi_Controller_Response_Rest_Renderer_Factory')
            ->disableOriginalConstructor()->getMock();
        $rendererFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_rendererMock));
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_ErrorProcessor')
            ->disableOriginalConstructor()->getMock();
        $helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')->disableOriginalConstructor()->getMock();
        $this->_appMock = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();

        /** Init SUP. */
        $this->_responseRest = new Mage_Webapi_Controller_Response_Rest(
            $rendererFactoryMock,
            $this->_errorProcessorMock,
            $helperMock,
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
     * Test setException method with Mage_Webapi_Exception.
     */
    public function testSetWebapiExceptionException()
    {
        /** Init Mage_Webapi_Exception */
        $apiException = new Mage_Webapi_Exception('Exception message.', 401);
        $this->_responseRest->setException($apiException);
        /** Assert that Mage_Webapi_Exception was set and presented in the list. */
        $this->assertTrue(
            $this->_responseRest->hasExceptionOfType('Mage_Webapi_Exception'),
            'Mage_Webapi_Exception was not set.'
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
            Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new Mage_Webapi_Exception('Message.', 400));
        $this->_responseRest->sendResponse();
    }

    /**
     * Test sendResponse method with HTTP Not Acceptable error exception during messages rendering.
     */
    public function testSendResponseRenderMessagesHttpNotAcceptable()
    {
        /** Init logic exception. */
        $logicException = new LogicException('Message', Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE);
        /** Mock renderer to throw LogicException in getMimeType method. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->throwException($logicException)
        );
        /** Assert that renderException method will be executed once with specified parameters. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $logicException,
            Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
        );
        /** Set exception to Rest response to get in to the _renderMessages method. */
        $this->_responseRest->setException(new Mage_Webapi_Exception('Message.', 400));
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
            'Mage_Webapi_Exception' => array(
                new Mage_Webapi_Exception('Message', 400),
                '{"errors":[{"code":400,"message":"Message"}]}',
                'Response sending with Mage_Webapi_Exception is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message', 100),
                '{"errors":[{"code":100,"message":"Message"}]}',
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
            'Mage_Webapi_Exception' => array(
                new Mage_Webapi_Exception('Message', 400),
                '{"errors":[{"code":400,"message":"Message","trace":"',
                'Response sending with Mage_Webapi_Exception in developer mode is invalid'
            ),
            'Logical Exception' => array(
                new LogicException('Message', 500),
                '{"errors":[{"code":500,"message":"Message","trace":"',
                'Response sending with Logical Exception in developer mode is invalid'
            ),
        );
    }

    /**
     * Test sendResponse method without any exception
     */
    public function testSendResponseSuccessHandling()
    {
        $this->_responseRest->sendResponse();
        $this->assertTrue($this->_responseRest->getHttpResponseCode() == Mage_Webapi_Controller_Response_Rest::HTTP_OK);
    }

    /**
     * Test sendResponse method with Mage_Service_ResourceNotFoundException
     *
     * @dataProvider dataProviderForSendResponseError404
     */
    public function testSendResponseError404($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );

        $this->_responseRest->setException($exception);
        $this->_responseRest->sendResponse();
        $this->assertTrue($this->_responseRest->getHttpResponseCode() == Mage_Webapi_Exception::HTTP_NOT_FOUND);

        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    public function dataProviderForSendResponseError404()
    {
        return array(
            'Mage_Service_ResourceNotFoundException' => array(
                new Mage_Service_ResourceNotFoundException('Resource not found', 2345),
                '{"errors":[{"code":2345,"message":"Resource not found"}]}',
                'Response sending with Mage_Service_ResourceNotFoundException is invalid'
            ),
        );
    }

    /**
     * Test sendResponse method with Mage_Service_AuthorizationException
     *
     * @dataProvider dataProviderForSendResponseError401
     */
    public function testSendResponseError401($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );

        $this->_responseRest->setException($exception);
        $this->_responseRest->sendResponse();
        $this->assertTrue($this->_responseRest->getHttpResponseCode() == Mage_Webapi_Exception::HTTP_UNAUTHORIZED);

        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    public function dataProviderForSendResponseError401()
    {
        return array(
            'Mage_Service_AuthorizationException' => array(
                new Mage_Service_AuthorizationException('Service authorization exception', 3456),
                '{"errors":[{"code":3456,"message":"Service authorization exception"}]}',
                'Response sending with Mage_Service_AuthorizationException is invalid'
            ),
        );
    }

    /**
     * Test sendResponse method with Mage_Service_Exception
     *
     * @dataProvider dataProviderForSendResponseError400
     */
    public function testSendResponseError400($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );

        $this->_responseRest->setException($exception);
        $this->_responseRest->sendResponse();
        $this->assertTrue($this->_responseRest->getHttpResponseCode() == Mage_Webapi_Exception::HTTP_BAD_REQUEST);

        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    public function dataProviderForSendResponseError400()
    {
        return array(
            'Mage_Service_Exception' => array(
                new Mage_Service_Exception('Generic service exception', 4567),
                '{"errors":[{"code":4567,"message":"Generic service exception"}]}',
                'Response sending with Mage_Service_Exception is invalid'
            ),
        );
    }

    /**
     * Test sendResponse method with any other Exception
     *
     * @dataProvider dataProviderForSendResponseError500
     */
    public function testSendResponseError500($exception, $expectedResult, $assertMessage)
    {
        /** Mock all required objects. */
        $this->_rendererMock->expects($this->any())->method('getMimeType')->will(
            $this->returnValue('application/json')
        );
        $this->_rendererMock->expects($this->any())->method('render')->will(
            $this->returnCallback(array($this, 'callbackForSendResponseTest'), $this->returnArgument(0))
        );

        $this->_responseRest->setException($exception);
        $this->_responseRest->sendResponse();
        $this->assertTrue($this->_responseRest->getHttpResponseCode() == Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);

        $actualResponse = $this->_responseRest->getBody();
        $this->assertEquals($expectedResult, $actualResponse, $assertMessage);
    }

    public function dataProviderForSendResponseError500()
    {
        return array(
            'Exception' => array(
                new Exception('Non service exception', 5678),
                '{"errors":[{"code":5678,"message":"Non service exception"}]}',
                'Response sending with any other Exception is invalid'
            ),
        );
    }
}
