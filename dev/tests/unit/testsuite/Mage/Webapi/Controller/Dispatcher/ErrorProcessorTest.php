<?php
/**
 * Test Webapi Error Processor.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_ErrorProcessorTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Core_Model_App */
    protected $_appMock;

    /** @var Mage_Core_Model_Logger */
    protected $_loggerMock;

    protected function setUp()
    {
        /** Set up mocks for SUT. */
        $this->_helperMock = $this->getMockBuilder('Mage_Core_Helper_Data')->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperFactoryMock = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')->getMock();
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));
        $this->_appMock = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_loggerMock = $this->getMockBuilder('Mage_Core_Model_Logger')->disableOriginalConstructor()->getMock();
        /** Initialize SUT. */
        $this->_errorProcessor = new Mage_Webapi_Controller_Dispatcher_ErrorProcessor(
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
     * Test render method in Json format.
     */
    public function testRenderJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Assert jsonEncode method will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode')->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_flush();
        $expectedResult = '{"messages":{"error":[{"code":500,"message":"Message"}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in Json.');
    }

    /**
     * Callback function for RenderJson and RenderJsonInDeveloperMode tests.
     *
     * Method encode data to Json and return it.
     *
     * @param $data
     * @return string
     */
    public function callbackJsonEncode($data)
    {
        return json_encode($data);
    }

    /**
     * Test render method in Json format with turned on developer mode.
     */
    public function testRenderJsonInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Mock app to return enabled developer mode flag. */
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        /** Assert jsonEncode method will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode')->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        ob_start();
        $this->_errorProcessor->render('Message', 'Message trace.', 401);
        $actualResult = ob_get_flush();
        $expectedResult = '{"messages":{"error":[{"code":401,"message":"Message","trace":"Message trace."}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in Json.');
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
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>500</code>'
            . '<message>Message</message></data_item></error></messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in XML.');
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
        $actualResult = ob_get_flush();
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>401</code><message>'
            . 'Message</message><trace><![CDATA[Trace message.]]></trace></data_item></error></messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong rendering in XML with turned on developer mode.');
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
        $this->markTestIncomplete("Think how to replace this test.");
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
        $this->markTestIncomplete("Think how to replace this test.");
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Init base Exception object. */
        $exception = new Exception('Message');
        /** Mock app to return enabled developer mode flag. */
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        /** Assert jsonEncode will be executed once. */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_errorProcessor->renderException($exception);
    }
}
