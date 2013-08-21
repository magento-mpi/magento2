<?php
/**
 * Test Magento_Webapi_Model_Soap_Wsdl_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Wsdl_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var Magento_Webapi_Model_Soap_Wsdl_Factory */
    protected $_soapWsdlFactory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
        $this->_soapWsdlFactory = new Magento_Webapi_Model_Soap_Wsdl_Factory($this->_objectManagerMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_soapWsdlFactory);
        parent::tearDown();
    }

    public function testCreate()
    {
        $wsdlName = 'wsdlName';
        $endpointUrl = 'endpointUrl';
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento_Webapi_Model_Soap_Wsdl', array('name' => $wsdlName, 'uri' => $endpointUrl));
        $this->_soapWsdlFactory->create($wsdlName, $endpointUrl);
    }
}
