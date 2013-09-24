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
    public function testGetLogicExceptionEmptyRequestAdapter()
    {
        $this->setExpectedException('LogicException', 'Request deserializer adapter is not set.');
        $interpreterFactory = new Magento_Webapi_Controller_Rest_Request_Deserializer_Factory(
            $this->getMock('Magento_ObjectManager'),
            array()
        );
        $interpreterFactory->get('contentType');
    }

    public function testGet()
    {
        $expectedMetadata = array('text_xml' => array('type' => 'text/xml', 'model' => 'Xml'));
        $validInterpreterMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Request_Deserializer_Xml')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($validInterpreterMock));

        $interpreterFactory = new Magento_Webapi_Controller_Rest_Request_Deserializer_Factory(
            $objectManagerMock,
            $expectedMetadata
        );
        $interpreterFactory->get('text/xml');
    }

    public function testGetMagentoWebapiException()
    {
        $expectedMetadata = array('text_xml' => array('type' => 'text/xml', 'model' => 'Xml'));
        $this->setExpectedException(
            'Magento_Webapi_Exception',
            'Server cannot understand Content-Type HTTP header media type text_xml'
        );
        $interpreterFactory = new Magento_Webapi_Controller_Rest_Request_Deserializer_Factory(
            $this->getMock('Magento_ObjectManager'),
            $expectedMetadata
        );
        $interpreterFactory->get('text_xml');
    }

    public function testGetLogicExceptionInvalidRequestDeserializer()
    {
        $expectedMetadata = array('text_xml' => array('type' => 'text/xml', 'model' => 'Xml'));
        $invalidInterpreter = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException(
            'LogicException',
            'The deserializer must implement "Magento_Webapi_Controller_Rest_Request_DeserializerInterface".'
        );
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($invalidInterpreter));

        $interpreterFactory = new Magento_Webapi_Controller_Rest_Request_Deserializer_Factory(
            $objectManagerMock,
            $expectedMetadata
        );
        $interpreterFactory->get('text/xml');
    }
}
