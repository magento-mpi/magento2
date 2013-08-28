<?php
/**
 * Helper for request data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest_Presentation_Request
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Controller_Rest_Request */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Controller_Request_Factory $requestFactory
    ) {
        $this->_helper = $helper;
        $this->_request = $requestFactory->get();
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @return array
     */
    public function getRequestData()
    {
        $requestParams = array_merge(
            $this->_getRequestBody(),
            $this->_request->getParams()
        );
        return $requestParams;
    }

    /**
     * Retrieve request data.
     *
     * @return array
     */
    protected function _getRequestBody()
    {
        $requestBody = array();
        $httpMethod = $this->_request->getHttpMethod();
        if ($httpMethod == Mage_Webapi_Model_Rest_Config::HTTP_METHOD_POST
            || $httpMethod == Mage_Webapi_Model_Rest_Config::HTTP_METHOD_PUT) {
            $requestBody = $this->_request->getBodyParams();
        }
        return $requestBody;
    }
}
