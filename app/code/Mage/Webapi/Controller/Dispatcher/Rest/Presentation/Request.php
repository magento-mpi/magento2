<?php
/**
 * Helper for request data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Request
{
    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_apiHelper;

    /** @var Mage_Webapi_Helper_Config */
    protected $_configHelper;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Config_Rest $apiConfig
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Helper_Config $configHelper
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     */
    public function __construct(
        Mage_Webapi_Model_Config_Rest $apiConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Helper_Config $configHelper,
        Mage_Webapi_Controller_Request_Factory $requestFactory
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_apiHelper = $helper;
        $this->_configHelper = $configHelper;
        $this->_request = $requestFactory->get();
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @return array
     */
    public function fetchRequestData()
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
        switch ($this->_request->getHttpMethod()) {
            // TODO: Introduce constants instead of literals
            case 'POST':
                // break is intentionally omitted
            case 'PUT':
                $requestBody = $this->_request->getBodyParams();
                break;
            case 'GET':
                // break is intentionally omitted
            case 'DELETE':
                // break is intentionally omitted
                break;
        }
        return $requestBody;
    }
}
