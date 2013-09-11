<?php
/**
 * Dispatcher for REST API calls.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher;

class Rest implements  \Magento\Webapi\Controller\DispatcherInterface
{
    /** @var \Magento\Webapi\Model\Config\Rest */
    protected $_apiConfig;

    /** @var \Magento\Webapi\Controller\Dispatcher\Rest\Presentation */
    protected $_restPresentation;

    /** @var \Magento\Webapi\Controller\Router\Rest */
    protected $_router;

    /** @var \Magento\Webapi\Controller\Dispatcher\Rest\Authentication */
    protected $_authentication;

    /** @var \Magento\Webapi\Controller\Request\Rest */
    protected $_request;

    /**
     * Action controller factory.
     *
     * @var \Magento\Webapi\Controller\Action\Factory
     */
    protected $_controllerFactory;

    /** @var \Magento\Webapi\Model\Authorization */
    protected $_authorization;

    /** @var \Magento\Webapi\Controller\Response\Rest */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config\Rest $apiConfig
     * @param \Magento\Webapi\Controller\Request\Rest $request
     * @param \Magento\Webapi\Controller\Response\Rest $response
     * @param \Magento\Webapi\Controller\Action\Factory $controllerFactory
     * @param \Magento\Webapi\Controller\Dispatcher\Rest\Presentation $restPresentation
     * @param \Magento\Webapi\Controller\Router\Rest $router
     * @param \Magento\Webapi\Model\Authorization $authorization
     * @param \Magento\Webapi\Controller\Dispatcher\Rest\Authentication $authentication
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Rest $apiConfig,
        \Magento\Webapi\Controller\Request\Rest $request,
        \Magento\Webapi\Controller\Response\Rest $response,
        \Magento\Webapi\Controller\Action\Factory $controllerFactory,
        \Magento\Webapi\Controller\Dispatcher\Rest\Presentation $restPresentation,
        \Magento\Webapi\Controller\Router\Rest $router,
        \Magento\Webapi\Model\Authorization $authorization,
        \Magento\Webapi\Controller\Dispatcher\Rest\Authentication $authentication
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_restPresentation = $restPresentation;
        $this->_router = $router;
        $this->_authentication = $authentication;
        $this->_request = $request;
        $this->_controllerFactory = $controllerFactory;
        $this->_authorization = $authorization;
        $this->_response = $response;
    }

    /**
     * Handle REST request.
     *
     * @return \Magento\Webapi\Controller\Dispatcher\Rest
     */
    public function dispatch()
    {
        try {
            $this->_authentication->authenticate();
            $route = $this->_router->match($this->_request);

            $operation = $this->_request->getOperationName();
            $resourceVersion = $this->_request->getResourceVersion();
            $this->_apiConfig->validateVersionNumber($resourceVersion, $this->_request->getResourceName());
            $method = $this->_apiConfig->getMethodNameByOperation($operation, $resourceVersion);
            $controllerClassName = $this->_apiConfig->getControllerClassByOperationName($operation);
            $controllerInstance = $this->_controllerFactory->createActionController(
                $controllerClassName,
                $this->_request
            );
            $versionAfterFallback = $this->_apiConfig->identifyVersionSuffix(
                $operation,
                $resourceVersion,
                $controllerInstance
            );
            /**
             * Route check has two stages:
             * The first is performed against full list of routes that is merged from all resources.
             * The second stage of route check can be performed only when actual version to be executed is known.
             */
            $this->_router->checkRoute($this->_request, $method, $versionAfterFallback);
            $this->_apiConfig->checkDeprecationPolicy($route->getResourceName(), $method, $versionAfterFallback);
            $action = $method . $versionAfterFallback;

            $this->_authorization->checkResourceAcl($route->getResourceName(), $method);

            $inputData = $this->_restPresentation->fetchRequestData($controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_restPresentation->prepareResponse($method, $outputData);
        } catch (\Exception $e) {
            $this->_response->setException($e);
        }
        $this->_response->sendResponse();
        return $this;
    }
}
