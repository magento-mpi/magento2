<?php
/**
 * Test Webapi Error Processor.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_ErrorProcessorTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Core_Helper_Data */
    protected $_helperMock;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_Core_Model_Logger */
    protected $_loggerMock;

    protected function setUp()
    {
        /** Set up mocks for SUT. */
        $this->_helperMock = $this->getMockBuilder('Magento_Core_Helper_Data')->disableOriginalConstructor()->getMock();
        $helperFactoryMock = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')->getMock();
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));
        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Magento_Core_Model_Logger')->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_errorProcessor = new Magento_Webapi_Controller_ErrorProcessor(
            $helperFactoryMock,
            $this->_appMock,
            $this->_loggerMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_errorProcessor);
        unset($this->_helperMock);
        unset($this->_appMock);
        parent::tearDown();
    }

    /**
     * Test render method in JSON format.
     */
    public function testRenderJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Assert that jsonEncode method will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode')->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '{"messages":{"error":[{"code":500,"message":"Message"}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in JSON.');
    }

    /**
     * Callback function for RenderJson and RenderJsonInDeveloperMode tests.
     *
     * Method encodes data to JSON and returns it.
     *
     * @param $data
     * @return string
     */
    public function callbackJsonEncode($data)
    {
        return json_encode($data);
    }

    /**
     * Test render method in JSON format with turned on developer mode.
     */
    public function testRenderJsonInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Mock app to return enabled developer mode flag. */
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        /** Assert that jsonEncode method will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode')->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        ob_start();
        $this->_errorProcessor->render('Message', 'Message trace.', 401);
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '{"messages":{"error":[{"code":401,"message":"Message","trace":"Message trace."}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in JSON.');
    }

    /**
     * Test render method in XML format.
     */
    public function testRenderXml()
    {
        $_SERVER['HTTP_ACCEPT'] = 'xml';
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>500</code>'
            . '<message><![CDATA[Message]]></message></data_item></error></messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in XML.');
    }

    /**
     * Test render method in XML format with turned on developer mode.
     */
    public function testRenderXmlInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'xml';
        /** Mock app to return enabled developer mode flag. */
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message', 'Trace message.', 401);
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>401</code><message>'
            . '<![CDATA[Message]]></message><trace><![CDATA[Trace message.]]></trace></data_item></error>'
            . '</messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in XML with turned on developer mode.');
    }

    /**
     * Test default render format is JSON.
     */
    public function testRenderDefaultFormat()
    {
        /** Set undefined rendering format. */
        $_SERVER['HTTP_ACCEPT'] = 'undefined';
        /** Assert that jsonEncode method will be executed at least once. */
        $this->_helperMock->expects($this->atLeastOnce())->method('jsonEncode');
        $this->_errorProcessor->render('Message');
    }

    /**
     * Test maskException method with Magento_Webapi_Exception.
     */
    public function testMaskWebapiException()
    {
        /** Init magento_webapi_Exception. */
        $apiException = new Magento_Webapi_Exception('Message', 400);
        /** Assert that Webapi exception was not masked. */
        $this->assertEquals(
            $this->_errorProcessor->maskException($apiException),
            $apiException,
            'Webapi Exception was masked wrong.'
        );
    }

    /**
     * Test maskException method with turned on developer mode.
     */
    public function testMaskExceptionInDeveloperMode()
    {
        /** Mock app isDeveloperMode to return true. */
        $this->_appMock->expects($this->once())->method('isDeveloperMode')->will($this->returnValue(true));
        /** Init Logical exception. */
        $errorMessage = 'Error Message';
        $logicalException = new LogicException($errorMessage);
        /** Assert that Logic exception is converted to Magento_Webapi_Exception without message obfuscation. */
        $maskedException = $this->_errorProcessor->maskException($logicalException);
        $this->assertInstanceOf('Magento_Webapi_Exception', $maskedException);
        $this->assertEquals(
            $errorMessage,
            $maskedException->getMessage(),
            'Exception was masked incorrectly in developer mode.'
        );
    }

    /**
     * Test maskException method with turned on developer mode.
     */
    public function testMaskNonWebapiException()
    {
        /** Assert that exception was logged. */
        $this->_loggerMock->expects($this->once())->method('logException');
        $exceptionMessage = 'Exception Message';
        $maskedException = $this->_errorProcessor->maskException(new LogicException($exceptionMessage));
        /** Assert that masked exception type is Magento_Webapi_Exception. */
        $this->assertInstanceOf('Magento_Webapi_Exception', $maskedException, 'Masked exception type is not Webapi.');
        /** Assert that masked exception code is 500. */
        $this->assertEquals(
            Magento_Webapi_Exception::HTTP_INTERNAL_ERROR,
            $maskedException->getHttpCode(),
            'Masked exception code is invalid.'
        );
        /** Assert masked exception message. */
        $this->assertContains(
            'Internal Error. Details are available in Magento log file. Report ID:',
            $maskedException->getMessage(),
            'Masked exception message is invalid.'
        );
    }
}
