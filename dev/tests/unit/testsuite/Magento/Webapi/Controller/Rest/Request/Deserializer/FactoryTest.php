<?php
/**
 * Test Webapi Json Deserializer Request Rest Controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Request_Deserializer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_applicationConfig;

    /** @var Magento_Webapi_Controller_Rest_Request_Deserializer_Factory */
    protected $_deserializerFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();
        $this->_applicationConfig = $this->getMockBuilder('Magento\Core\Model\Config')
            ->setMethods(array('getNode'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_deserializerFactory = new Magento_Webapi_Controller_Rest_Request_Deserializer_Factory(
            $this->_objectManagerMock,
            $this->_applicationConfig
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_applicationConfig);
        unset($this->_deserializerFactory);
        parent::tearDown();
    }

    public function testGetLogicExceptionEmptyRequestAdapter()
    {
        $this->setExpectedException('LogicException', 'Request deserializer adapter is not set.');
        $this->_applicationConfig->expects($this->once())->method('getNode')->will($this->returnValue(null));
        $this->_deserializerFactory->get('contentType');
    }

    public function testGet()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));
        $validDeserializer = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Request_Deserializer_Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($validDeserializer));
        /** Initialize SUT. */
        $this->_deserializerFactory->get('text/xml');
    }

    public function testGetMagentoWebapiException()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));
        $exceptionMessage = 'Server cannot understand Content-Type HTTP header media type text_xml';
        /** Initialize SUT. */
        try {
            $this->_deserializerFactory->get('text_xml');
            $this->fail("Exception is expected to be raised");
        } catch (Magento_Webapi_Exception $e) {
            $this->assertInstanceOf('Magento_Webapi_Exception', $e, 'Exception type is invalid');
            $this->assertEquals($exceptionMessage, $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(Magento_Webapi_Exception::HTTP_BAD_REQUEST, $e->getHttpCode(), 'HTTP code is invalid');
        }
    }

    public function testGetLogicExceptionInvalidRequestDeserializer()
    {
        /** Prepare mocks for SUT constructor. */
        $expectedMetadata = new SimpleXMLElement('<text_xml><type>text/xml</type><model>Xml</model></text_xml>');
        $this->_applicationConfig
            ->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue(array($expectedMetadata)));
        $invalidDeserializer = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Response_Renderer_Json')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($invalidDeserializer));
        $this->setExpectedException(
            'LogicException',
            'The deserializer must implement "Magento_Webapi_Controller_Rest_Request_DeserializerInterface".'
        );
        /** Initialize SUT. */
        $this->_deserializerFactory->get('text/xml');
    }
}
