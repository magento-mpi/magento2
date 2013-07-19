<?php
/**
 * SOAP API Request Test.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_SoapTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var Mage_Core_Model_Config */
    protected $_configMock;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_soapRequest;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->setMethods(array('getNode'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configMock->expects($this->once())
            ->method('getNode')
            ->with($this->anything())
            ->will($this->returnValue('testNode'));

        /** Initialize SUT. */
        $this->_soapRequest = new Mage_Webapi_Controller_Request_Soap($this->_configMock, $this->_helperMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
        unset($this->_configMock);
        unset($this->_soapRequest);
        parent::tearDown();
    }

    public function testGetRequestedResourcesNotAllowedParametersException()
    {
        /** Prepare mocks for SUT constructor. */
        $wsdlParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $resourcesParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES;
        // Set two not allowed parameters and all allowed
        $requestParams = array(
            'param_1' => 'foo',
            'param_2' => 'bar',
            $wsdlParam => true,
            Mage_Webapi_Controller_Request::PARAM_API_TYPE => true,
            $resourcesParam => true
        );
        $this->_soapRequest->setParams($requestParams);
        $this->_helperMock->expects($this->at(0))
            ->method('__')
            ->with('Not allowed parameters: %s. ', 'param_1, param_2')
            ->will($this->returnValue('Not allowed parameters: param_1, param_2. '));
        $this->_helperMock->expects($this->at(1))
            ->method('__')
            ->with('Please use only "%s" and "%s".', $wsdlParam, $resourcesParam)
            ->will($this->returnValue('Please use only "' . $wsdlParam . '" and "' . $resourcesParam . '".'));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Not allowed parameters: param_1, param_2. Please use only "'
            . $wsdlParam . '" and "' . $resourcesParam . '".',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResourcesEmptyRequestedResourcesException()
    {
        /** Prepare mocks for SUT constructor. */
        $requestParams = array(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES => null);
        $this->_soapRequest->setParams($requestParams);
        $this->_helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Incorrect format of WSDL request URI or Requested resources are missing',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResourcesSameRequestedResourcesException()
    {
        $resource = "testModule1AllSoapAndRest";
        $expectedMsg = 'Resource"' . $resource . '" cannot be requested more than once';
        $requestParams = array(
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL => true,
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES => "$resource:V1,$resource:V2"
        );
        $this->_soapRequest->setParams($requestParams);

        $this->_helperMock->expects($this->at(0))
            ->method('__')
            ->with('Resource "%s" cannot be requested more than once', "testModule1AllSoapAndRest")
            ->will($this->returnValue($expectedMsg));

        $this->setExpectedException(
            'Mage_Webapi_Exception',
            $expectedMsg,
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );

        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResourcesSuccess()
    {
        $resourceA = "testModule1AllSoapAndRest";
        $resourceB = "testModule2AllSoapNoRest";
        $requestParams = array(
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL => true,
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES => "$resourceA:V1,$resourceB:V2"
        );
        $this->_soapRequest->setParams($requestParams);

        $expected = array(
            $resourceA => 'V1',
            $resourceB => 'V2',
        );
        $this->assertEquals(
            $expected,
            $this->_soapRequest->getRequestedResources()
        );
    }
}
