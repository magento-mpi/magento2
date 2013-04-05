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

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_soapRequest;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_soapRequest = new Mage_Webapi_Controller_Request_Soap($this->_helperMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
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
        $this->_helperMock->expects($this->once())
            ->method('__')
            ->will($this->returnArgument(0));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Requested resources are missing.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResources()
    {
        /** Prepare mocks for SUT constructor. */
        $resources = array('serviceName_1' => 'version', 'serviceName_2' => 'version');
        $requestParams = array(
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL => true,
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES => $resources,
            Mage_Webapi_Controller_Request::PARAM_API_TYPE => 'soap'
        );
        $this->_soapRequest->setParams($requestParams);
        /** Execute SUT. */
        $this->assertEquals(
            $resources,
            $this->_soapRequest->getRequestedResources(),
            'Requested resources were retrieved incorrectly. '
        );
    }
}
