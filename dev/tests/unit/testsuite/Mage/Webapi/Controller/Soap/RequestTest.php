<?php
/**
 * SOAP API Request Test.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Soap_RequestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_soapRequest;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $applicationMock = $this->getMockBuilder('Mage_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $applicationMock->expects($this->once())->method('getConfig')->will($this->returnValue($configMock));
        $configMock->expects($this->once())->method('getAreaFrontName')->will($this->returnValue('soap'));

        /** Initialize SUT. */
        $this->_soapRequest = new Mage_Webapi_Controller_Soap_Request($applicationMock, $this->_helperMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
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
        $exceptionMessage = 'Not allowed parameters: param_1, param_2. Please use only "'
            . $wsdlParam . '" and "' . $servicesParam . '".';
        /** Execute SUT. */
        try {
            $this->_soapRequest->getRequestedServices();
            $this->fail("Exception is expected to be raised");
        } catch (Mage_Webapi_Exception $e) {
            $this->assertInstanceOf('Mage_Webapi_Exception', $e, 'Exception type is invalid');
            $this->assertEquals($exceptionMessage, $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(Mage_Webapi_Exception::HTTP_BAD_REQUEST, $e->getHttpCode(), 'HTTP code is invalid');
        }
    }

    public function testGetRequestedServicesEmptyRequestedServicesException()
    {
        /** Prepare mocks for SUT constructor. */
        $requestParams = array(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES => null);
        $this->_soapRequest->setParams($requestParams);
        $this->_helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $exceptionMessage = 'Incorrect format of WSDL request URI or Requested services are missing.';
        /** Execute SUT. */
        try {
            $this->_soapRequest->getRequestedServices();
            $this->fail("Exception is expected to be raised");
        } catch (Mage_Webapi_Exception $e) {
            $this->assertInstanceOf('Mage_Webapi_Exception', $e, 'Exception type is invalid');
            $this->assertEquals($exceptionMessage, $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(Mage_Webapi_Exception::HTTP_BAD_REQUEST, $e->getHttpCode(), 'HTTP code is invalid');
        }
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
