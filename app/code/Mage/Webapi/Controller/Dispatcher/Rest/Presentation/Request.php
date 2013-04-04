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
    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_apiHelper;

    /** @var Mage_Webapi_Helper_Config */
    protected $_configHelper;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Helper_Config $configHelper
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     */
    public function __construct(
        Mage_Core_Service_Config $serviceConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Helper_Config $configHelper,
        Mage_Webapi_Controller_Request_Factory $requestFactory
    ) {
        $this->_serviceConfig = $serviceConfig;
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
        // TDOO: Consider reimplementation of code below according to new requirements
        $requestParams = $this->_request->getParams();
//        $methodReflection = Mage_Webapi_Helper_Data::createMethodReflection($controllerInstance, $action);
//        $bodyParamName = $this->_configHelper->getOperationBodyParamName($methodReflection);
//        $requestParams = array_merge(
//            $this->_request->getParams(),
//            array($bodyParamName => $this->_getRequestBody($this->_request->getHttpMethod()))
//        );
//        /** Convert names of ID and Parent ID params in request to those which are used in method interface. */
//        $idArgumentName = $this->_configHelper->getOperationIdParamName($methodReflection);
//        $parentIdParamName = Mage_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
//        $idParamName = Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID;
//        if (isset($requestParams[$parentIdParamName]) && ($idArgumentName != $parentIdParamName)) {
//            $requestParams[$idArgumentName] = $requestParams[$parentIdParamName];
//            unset($requestParams[$parentIdParamName]);
//        } elseif (isset($requestParams[$idParamName]) && ($idArgumentName != $idParamName)) {
//            $requestParams[$idArgumentName] = $requestParams[$idParamName];
//            unset($requestParams[$idParamName]);
//        }

        return $this->_apiHelper->prepareMethodParams(
            $controllerInstance,
            $action,
            $requestParams,
            $this->_serviceConfig
        );
    }

    /**
     * Retrieve request data. Ensure that data is not empty.
     *
     * @param string $httpMethod
     * @return array
     */
    protected function _getRequestBody($httpMethod)
    {
        // TODO: Implement filtration of item and collection requests
        $processedInputData = null;
        switch ($httpMethod) {
            case Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_POST:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration of item
                break;
            case Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_PUT:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration
                break;
            case Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_GET:
                // break is intentionally omitted
            case Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_DELETE:
                break;
        }
        return $processedInputData;
    }
}
