<?php
/**
 * Test Webapi Json Interpreter Request Rest Controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Rest_Interpreter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_applicationConfig;

    /** @var Magento_Webapi_Controller_Request_Rest_Interpreter_Factory */
    protected $_interpreterFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $this->_applicationConfig = $this->getMockBuilder('Magento_Core_Model_Config')
            ->setMethods(array('getNode'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_interpreterFactory = new Magento_Webapi_Controller_Request_Rest_Interpreter_Factory(
            $this->_objectManagerMock,
            $this->_applicationConfig
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_applicationConfig);
        unset($this->_interpreterFactory);
        parent::tearDown();
    }

    public function testGetLogicExceptionEmptyRequestAdapter()
    {
        $this->setExpectedException('LogicException', 'Request interpreter adapter is not set.');
        $this->_applicationConfig->expects($this->once())->method('getNode')->will($this->returnValue(null));
        $this->_interpreterFactory->get('contentType');
    }

    public function testGet()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));
        $validInterpreterMock = $this->getMockBuilder('Magento_Webapi_Controller_Request_Rest_Interpreter_Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($validInterpreterMock));
        /** Initialize SUT. */
        $this->_interpreterFactory->get('text/xml');
    }

    public function testGetMageWebapiException()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));

        $this->setExpectedException(
            'Magento_Webapi_Exception',
            'Server cannot understand Content-Type HTTP header media type "text_xml"',
            Magento_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Initialize SUT. */
        $this->_interpreterFactory->get('text_xml');
    }

    public function testGetLogicExceptionInvalidRequestInterpreter()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));
        $invalidInterpreter = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($invalidInterpreter));
        $this->setExpectedException(
            'LogicException',
            'The interpreter must implement "Magento_Webapi_Controller_Request_Rest_InterpreterInterface".'
        );
        /** Initialize SUT. */
        $this->_interpreterFactory->get('text/xml');
    }
}
