<?php
/**
 * Helper for request data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher\Rest\Presentation;

class Request
{
    /** @var \Magento\Webapi\Model\Config\Rest */
    protected $_apiConfig;

    /** @var \Magento\Webapi\Helper\Data */
    protected $_apiHelper;

    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /** @var \Magento\Webapi\Controller\Request\Rest */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config\Rest $apiConfig
     * @param \Magento\Webapi\Helper\Data $helper
     * @param \Magento\Webapi\Helper\Config $configHelper
     * @param \Magento\Webapi\Controller\Request\Factory $requestFactory
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Rest $apiConfig,
        \Magento\Webapi\Helper\Data $helper,
        \Magento\Webapi\Helper\Config $configHelper,
        \Magento\Webapi\Controller\Request\Factory $requestFactory
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
        $methodReflection = \Magento\Webapi\Helper\Data::createMethodReflection($controllerInstance, $action);
        $methodName = $this->_configHelper->getMethodNameWithoutVersionSuffix($methodReflection);
        $bodyParamName = $this->_configHelper->getOperationBodyParamName($methodReflection);
        $requestParams = array_merge(
            $this->_request->getParams(),
            array($bodyParamName => $this->_getRequestBody($methodName))
        );
        /** Convert names of ID and Parent ID params in request to those which are used in method interface. */
        $idArgumentName = $this->_configHelper->getOperationIdParamName($methodReflection);
        $parentIdParamName = \Magento\Webapi\Controller\Router\Route\Rest::PARAM_PARENT_ID;
        $idParamName = \Magento\Webapi\Controller\Router\Route\Rest::PARAM_ID;
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
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration of item
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement fields filtration
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_DELETE:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_GET:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_DELETE:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_LIST:
                break;
        }
        return $processedInputData;
    }
}
