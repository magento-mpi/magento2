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
    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Core_Model_Config */
    protected $_configMock;

    /** @var Mage_Webapi_Controller_Soap_Request */
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

        /** Initialize SUT. */
        $this->_soapRequest = new Mage_Webapi_Controller_Soap_Request($this->_configMock, $this->_helperMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
        unset($this->_configMock);
        unset($this->_soapRequest);
        parent::tearDown();
    }

    public function testGetRequestedServicesNotAllowedParametersException()
    {
        /** Prepare mocks for SUT constructor. */
        $wsdlParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $servicesParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES;
        // Set two not allowed parameters and all allowed
        $requestParams = array(
            'param_1' => 'foo',
            'param_2' => 'bar',
            $wsdlParam => true,
            Mage_Webapi_Controller_Request::PARAM_REQUEST_TYPE => true,
            $servicesParam => true
        );
        $this->_soapRequest->setParams($requestParams);
        $this->_helperMock->expects($this->at(0))
            ->method('__')
            ->with('Not allowed parameters: %s. ', 'param_1, param_2')
            ->will($this->returnValue('Not allowed parameters: param_1, param_2. '));
        $this->_helperMock->expects($this->at(1))
            ->method('__')
            ->with('Please use only "%s" and "%s".', $wsdlParam, $servicesParam)
            ->will($this->returnValue('Please use only "' . $wsdlParam . '" and "' . $servicesParam . '".'));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Not allowed parameters: param_1, param_2. Please use only "'
            . $wsdlParam . '" and "' . $servicesParam . '".',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedServices();
    }

    public function testGetRequestedServicesEmptyRequestedServicesException()
    {
        /** Prepare mocks for SUT constructor. */
        $requestParams = array(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES => null);
        $this->_soapRequest->setParams($requestParams);
        $this->_helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Incorrect format of WSDL request URI or Requested services are missing',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedServices();
    }

    /**
     * @dataProvider providerTestGetRequestedServicesSuccess
     * @param $requestParamServices
     * @param $expectedResult
     */
    public function testGetRequestedServicesSuccess($requestParamServices, $expectedResult)
    {
        $requestParams = array(
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL => true,
            Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES => $requestParamServices
        );
        $this->_soapRequest->setParams($requestParams);
        $this->assertEquals($expectedResult, $this->_soapRequest->getRequestedServices());
    }

    public function providerTestGetRequestedServicesSuccess()
    {
        $testModuleA = 'testModule1AllSoapAndRestV1';
        $testModuleB = 'testModule1AllSoapAndRestV2';
        $testModuleC = 'testModule2AllSoapNoRestV1';
        return array(
            array(
                "{$testModuleA},{$testModuleB}",
                array(
                    $testModuleA,
                    $testModuleB
                )
            ),
            array(
                "{$testModuleA},{$testModuleC}",
                array(
                    $testModuleA,
                    $testModuleC
                )
            ),
            array(
                "{$testModuleA}",
                array(
                    $testModuleA
                )
            )
        );
    }
}
