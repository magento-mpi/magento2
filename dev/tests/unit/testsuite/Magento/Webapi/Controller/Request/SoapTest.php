<?php
/**
 * SOAP API Request Test.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_SoapTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Request\Soap */
    protected $_soapRequest;

    protected function setUp()
    {
        /** Initialize SUT. */
        $this->_soapRequest = new \Magento\Webapi\Controller\Request\Soap();
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapRequest);
        parent::tearDown();
    }

    public function testGetRequestedResourcesNotAllowedParametersException()
    {
        /** Prepare mocks for SUT constructor. */
        $wsdlParam = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL;
        $resourcesParam = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_RESOURCES;
        // Set two not allowed parameters and all allowed
        $requestParams = array(
            'param_1' => 'foo',
            'param_2' => 'bar',
            $wsdlParam => true,
            \Magento\Webapi\Controller\Request::PARAM_API_TYPE => true,
            $resourcesParam => true
        );
        $this->_soapRequest->setParams($requestParams);
        $this->setExpectedException(
            '\Magento\Webapi\Exception',
            'Not allowed parameters: param_1, param_2. Please use only "'
                . $wsdlParam . '" and "' . $resourcesParam . '".',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResourcesEmptyRequestedResourcesException()
    {
        /** Prepare mocks for SUT constructor. */
        $requestParams = array(\Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_RESOURCES => null);
        $this->_soapRequest->setParams($requestParams);
        $this->setExpectedException(
            '\Magento\Webapi\Exception',
            'Requested resources are missing.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapRequest->getRequestedResources();
    }

    public function testGetRequestedResources()
    {
        /** Prepare mocks for SUT constructor. */
        $resources = array('resourceName_1' => 'version', 'resourceName_2' => 'version');
        $requestParams = array(
            \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL => true,
            \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_RESOURCES => $resources,
            \Magento\Webapi\Controller\Request::PARAM_API_TYPE => 'soap'
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
