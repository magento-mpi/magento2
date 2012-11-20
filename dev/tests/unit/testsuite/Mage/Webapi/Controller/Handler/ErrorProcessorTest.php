<?php
/**
 * Test Webapi Error Processor.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Handler_ErrorProcessorTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Handler_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Core_Helper_Data */
    protected $_helperMock;

    protected function setUp()
    {
        /** Set up mocks for SUT. */
        $this->_helperMock = $this->getMockBuilder('Mage_Core_Helper_Data')->getMock();
        $helperFactoryMock = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')->getMock();
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));
        /** Initialize SUT. */
        $this->_errorProcessor = new Mage_Webapi_Controller_Handler_ErrorProcessor($helperFactoryMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_errorProcessor);
        parent::tearDown();
    }

    /**
     * Test render method in Json format.
     */
    public function testRenderJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Assert jsonEncode method will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_errorProcessor->render('Message');
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
        $actualResult = ob_get_flush();
        $expectedResult = '<?xml version="1.0"?><magento_api><messages><error><data_item><code>500</code>'
            . '<message>Message</message></data_item></error></messages></magento_api>';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in XML.');
    }

    /**
     * Test render method in XML format with turned on developer mode.
     */
    public function testRenderXmlInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'xml';
        /** Turn on developer mode. */
        Mage::setIsDeveloperMode(true);
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message', 'Trace message.', 401);
        /** Get output buffer. */
        $actualResult = ob_get_flush();
        $expectedResult = '<?xml version="1.0"?><magento_api><messages><error><data_item><code>401</code>'
            . '<message>Message</message><trace><![CDATA[Trace message.]]></trace></data_item></error>'
            . '</messages></magento_api>';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in XML with turned on developer mode.');
    }

    /**
     * Test render method in Url Encode Query format.
     */
    public function testRenderUrlEncodeQuery()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/plain';
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_flush();
        $expectedResult = 'messages%5Berror%5D%5B0%5D%5Bcode%5D=500&messages%5Berror%5D%5B0%5D%5Bmessage%5D=Message'
            . '&messages%5Berror%5D%5B0%5D%5Btrace%5D=Trace+is+not+available.';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in URL encode.');
    }

    /**
     * Test render method in Url Encode Query format with turned on developer mode.
     */
    public function testRenderUrlEncodeQueryInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/plain';
        /** Turn on Developer mode. */
        Mage::setIsDeveloperMode(true);
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message', 'Trace message.', 401);
        /** Get output buffer. */
        $actualResult = ob_get_flush();
        $expectedResult = 'messages%5Berror%5D%5B0%5D%5Bcode%5D=401&messages%5Berror%5D%5B0%5D%5Bmessage%5D=Message'
            . '&messages%5Berror%5D%5B0%5D%5Btrace%5D=Trace+message.';
        $this->assertEquals(
            $expectedResult,
            $actualResult,
            'Wrong rendering in URL encode format with turned on developer mode.'
        );
    }

    /**
     * Test default render format is Json.
     */
    public function testRenderDefaultFormat()
    {
        /** Set undefined rendering format. */
        $_SERVER['HTTP_ACCEPT'] = 'undefined';
        /** Assert jsonEncode method will be executed at least once. */
        $this->_helperMock->expects($this->atLeastOnce())->method('jsonEncode');
        $this->_errorProcessor->render('Message');
    }

    /**
     * Test renderException method with Mage_Webapi_Exception.
     */
    public function testRenderExecutionWebapiException()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Init Mage_Webapi_Exception. */
        $apiException = new Mage_Webapi_Exception('Exception message', 500);
        /** Assert jsonEncode will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_errorProcessor->renderException($apiException);
    }

    /**
     * Test renderException method with turned on Developer mode.
     */
    public function testRenderExecutionInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Init base Exception object. */
        $exception = new Exception('Message');
        /** Turn on developer mode. */
        Mage::setIsDeveloperMode(true);
        /** Assert jsonEncode will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_errorProcessor->renderException($exception);
    }
}
