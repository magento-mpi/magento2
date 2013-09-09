<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Request_Interpreter_XmlTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_xmlParserMock;

    /** @var Magento_Webapi_Controller_Rest_Request_Interpreter_Xml */
    protected $_xmlInterpreter;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_appMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_xmlParserMock = $this->getMock('Magento_Xml_Parser', array('xmlToArray', 'loadXML'));
        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')
            ->setMethods(array('isDeveloperMode'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_xmlInterpreter = new Magento_Webapi_Controller_Rest_Request_Interpreter_Xml(
            $this->_xmlParserMock,
            $this->_appMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_xmlInterpreter);
        unset($this->_xmlParserMock);
        unset($this->_appMock);
        parent::tearDown();
    }

    public function testInterpretInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException', '"boolean" data type is invalid. String is expected.');
        $this->_xmlInterpreter->interpret(false);
    }

    public function testInterpret()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_xmlParserMock->expects($this->once())->method('loadXML');
        $validInputXml = '<?xml version="1.0"?><xml><key1>test1</key1><key2>test2</key2></xml>';
        $returnArray = array('xml' => array('key1' => 'test1', 'key2' => 'test2'));
        $this->_xmlParserMock->expects($this->once())->method('xmlToArray')->will($this->returnValue($returnArray));
        $expectedArray = array('key1' => 'test1', 'key2' => 'test2');
        /** Initialize SUT. */
        $this->assertEquals(
            $expectedArray,
            $this->_xmlInterpreter->interpret($validInputXml),
            'Request XML body was parsed incorrectly into array of params.'
        );
    }

    public function testHandleErrors()
    {
        /** Add error message */
        $firstErrorMessage = "No document type declaration. ";
        $this->_xmlInterpreter->handleErrors(null, $firstErrorMessage, null, null);
        /** Assert that first error message was added */
        $this->assertAttributeEquals(
            $firstErrorMessage,
            '_errorMessage',
            $this->_xmlInterpreter,
            'Error message was not set to xml interpreter.'
        );
        /** Add error message */
        $secondErrorMessage = "Strings should be wrapped in double quotes.";
        $expectedMessages = $firstErrorMessage . $secondErrorMessage;
        $this->_xmlInterpreter->handleErrors(null, $secondErrorMessage, null, null);
        /** Assert that both error messages were added */
        $this->assertAttributeEquals(
            $expectedMessages,
            '_errorMessage',
            $this->_xmlInterpreter,
            'Error messages were not set to xml interpreter.'
        );
    }

    public function testInterpretMagentoWebapiExceptionDeveloperModeOn()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_appMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(true));
        $errorMessage = 'End tag for "key1" was omitted.';
        $this->_xmlInterpreter->handleErrors(null, $errorMessage, null, null);
        $this->_xmlParserMock->expects($this->once())->method('loadXML');
        $invalidXml = '<?xml version="1.0"?><xml><key1>test1</xml>';
        /** Initialize SUT. */
        try {
            $this->_xmlInterpreter->interpret($invalidXml);
            $this->fail("Exception is expected to be raised");
        } catch (Magento_Webapi_Exception $e) {
            $exceptionMessage = 'Decoding Error: End tag for "key1" was omitted.';
            $this->assertInstanceOf('Magento_Webapi_Exception', $e, 'Exception type is invalid');
            $this->assertEquals($exceptionMessage, $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(Magento_Webapi_Exception::HTTP_BAD_REQUEST, $e->getHttpCode(), 'HTTP code is invalid');
        }
    }

    public function testInterpretMagentoWebapiExceptionDeveloperModeOff()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_appMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(false));
        $errorMessage = 'End tag for "key1" was omitted.';
        $this->_xmlInterpreter->handleErrors(null, $errorMessage, null, null);
        $this->_xmlParserMock->expects($this->once())->method('loadXML');
        $invalidXml = '<?xml version="1.0"?><xml><key1>test1</xml>';
        /** Initialize SUT. */
        try {
            $this->_xmlInterpreter->interpret($invalidXml);
            $this->fail("Exception is expected to be raised");
        } catch (Magento_Webapi_Exception $e) {
            $this->assertInstanceOf('Magento_Webapi_Exception', $e, 'Exception type is invalid');
            $this->assertEquals('Decoding error.', $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(Magento_Webapi_Exception::HTTP_BAD_REQUEST, $e->getHttpCode(), 'HTTP code is invalid');
        }
    }
}
