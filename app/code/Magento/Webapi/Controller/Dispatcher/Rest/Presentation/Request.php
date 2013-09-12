<?php
/**
 * Helper for request data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Request
{
    /** @var Magento_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /** @var Magento_Webapi_Helper_Data */
    protected $_apiHelper;

    /** @var Magento_Webapi_Helper_Config */
    protected $_configHelper;

    /** @var Magento_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Rest $apiConfig
     * @param Magento_Webapi_Helper_Data $helper
     * @param Magento_Webapi_Helper_Config $configHelper
     * @param Magento_Webapi_Controller_Request_Factory $requestFactory
     */
    public function __construct(
        Magento_Webapi_Model_Config_Rest $apiConfig,
        Magento_Webapi_Helper_Data $helper,
        Magento_Webapi_Helper_Config $configHelper,
        Magento_Webapi_Controller_Request_Factory $requestFactory
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_apiHelper = $helper;
        $this->_configHelper = $configHelper;
        $this->_request = $requestFactory->get();
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @param object $controllerInstance
     * @param string $action
     * @return array
     */
    public function fetchRequestData($controllerInstance, $action)
    {
        $methodReflection = Magento_Webapi_Helper_Data::createMethodReflection($controllerInstance, $action);
        $methodName = $this->_configHelper->getMethodNameWithoutVersionSuffix($methodReflection);
        $bodyParamName = $this->_configHelper->getOperationBodyParamName($methodReflection);
        $requestParams = array_merge(
            $this->_request->getParams(),
            array($bodyParamName => $this->_getRequestBody($methodName))
        );
        /** Convert names of ID and Parent ID params in request to those which are used in method interface. */
        $idArgumentName = $this->_configHelper->getOperationIdParamName($methodReflection);
        $parentIdParamName = Magento_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
        $idParamName = Magento_Webapi_Controller_Router_Route_Rest::PARAM_ID;
        if (isset($requestParams[$parentIdParamName]) && ($idArgumentName != $parentIdParamName)) {
            $requestParams[$idArgumentName] = $requestParams[$parentIdParamName];
            unset($requestParams[$parentIdParamName]);
        } elseif (isset($requestParams[$idParamName]) && ($idArgumentName != $idParamName)) {
            $requestParams[$idArgumentName] = $requestParams[$idParamName];
            unset($requestParams[$idParamName]);
        }

        return $this->_apiHelper->prepareMethodParams($controllerInstance, $action, $requestParams, $this->_apiConfig);
    }

    /**
     * Retrieve request data. Ensure that data is not empty.
     *
     * @param string $method
     * @return array
     */
    protected function _getRequestBody($method)
    {
        $processedInputData = null;
        switch ($method) {
            case Magento_Webapi_Controller_ActionAbstract::METHOD_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration of item
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement fields filtration
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_GET:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_DELETE:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_LIST:
                break;
        }
        return $processedInputData;
    }
}
