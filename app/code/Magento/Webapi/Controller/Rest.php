<?php
/**
 * Front controller for WebAPI REST area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest implements Magento_Core_Controller_FrontInterface
{
    /** @var Magento_Webapi_Controller_Rest_Router */
    protected $_router;

    /** @var Magento_Webapi_Controller_Rest_Request */
    protected $_request;

    /** @var Magento_Webapi_Controller_Rest_Response */
    protected $_response;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Magento_Core_Model_App_State */
    protected $_appState;

    /** @var Magento_Webapi_Controller_Rest_Authentication */
    protected $_authentication;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Rest_Request $request
     * @param Magento_Webapi_Controller_Rest_Response $response
     * @param Magento_Webapi_Controller_Rest_Router $router
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Webapi_Controller_Rest_Authentication $authentication
     */
    public function __construct(
        Magento_Webapi_Controller_Rest_Request $request,
        Magento_Webapi_Controller_Rest_Response $response,
        Magento_Webapi_Controller_Rest_Router $router,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_App_State $appState,
        Magento_Webapi_Controller_Rest_Authentication $authentication
    ) {
        $this->_router = $router;
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        $this->_authentication = $authentication;
    }

    /**
     * Initialize front controller
     *
     * @return Magento_Webapi_Controller_Rest
     */
    public function init()
    {
        return $this;
    }

    /**
     * Handle REST request.
     *
     * @return Magento_Webapi_Controller_Rest
     */
    public function dispatch()
    {
        try {
            if (!$this->_appState->isInstalled()) {
                throw new Magento_Webapi_Exception(__('Magento is not yet installed'));
            }
//            $this->_authentication->authenticate($this->_request);
            $route = $this->_router->match($this->_request);

            if ($route->isSecure() && !$this->_request->isSecure()) {
                throw new Magento_Webapi_Exception(__('Operation allowed only in HTTPS'));
            }
            /** @var array $inputData */
            $inputData = $this->_request->getRequestData();
            $serviceMethod = $route->getServiceMethod();
            $service = $this->_objectManager->get($route->getServiceClass());
            $outputData = $service->$serviceMethod($inputData);
            if (!is_array($outputData)) {
                throw new LogicException(
                    sprintf('The method "%s" of service "%s" must return an array.', $serviceMethod,
                        $route->getServiceClass())
                );
            }
            $this->_response->prepareResponse($outputData);
        } catch (Exception $e) {
            $this->_response->setException($e);
        }
        $this->_response->sendResponse();
        return $this;
    }
}
