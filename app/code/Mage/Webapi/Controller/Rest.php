<?php
/**
 * Front controller for WebAPI REST area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest implements Mage_Core_Controller_FrontInterface
{
    const REQUEST_TYPE = 'rest';

    /** @var Mage_Webapi_Controller_Rest_Presentation */
    protected $_restPresentation;

    /** @var Mage_Webapi_Controller_Rest_Router */
    protected $_router;

    /** @var Mage_Webapi_Controller_Rest_Authentication */
    protected $_authentication;

    /** @var Mage_Webapi_Controller_Rest_Request */
    protected $_request;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Mage_Webapi_Controller_Rest_Response */
    protected $_response;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_App_State */
    protected $_appState;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Rest_Request $request
     * @param Mage_Webapi_Controller_Rest_Response $response
     * @param Mage_Webapi_Controller_Rest_Presentation $restPresentation
     * @param Mage_Webapi_Controller_Rest_Router $router
     * @param Mage_Webapi_Controller_Rest_Authentication $authentication
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Webapi_Helper_Data $helper,
     * @param Mage_Core_Model_App_State $appState
     */
    public function __construct(
        Mage_Webapi_Controller_Rest_Request $request,
        Mage_Webapi_Controller_Rest_Response $response,
        Mage_Webapi_Controller_Rest_Presentation $restPresentation,
        Mage_Webapi_Controller_Rest_Router $router,
        // TODO: Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Rest_Authentication $authentication,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Helper_Data $helper,
        Mage_Core_Model_App_State $appState
    ) {
        $this->_restPresentation = $restPresentation;
        $this->_router = $router;
        $this->_authentication = $authentication;
        // TODO: $this->_authorization = $authorization;
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_appState = $appState;
    }

    /**
     * Initialize front controller
     *
     * @return Mage_Webapi_Controller_Rest
     */
    public function init()
    {
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);

        return $this;
    }

    /**
     * Handle REST request.
     *
     * @return Mage_Webapi_Controller_Rest
     */
    public function dispatch()
    {
        if (!$this->_appState->isInstalled()) {
            $this->_response->setException(
                new Mage_Webapi_Exception(
                    $this->_helper->__('Magento is not yet installed'),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST
                )
            );
        } else {
            try {
                // TODO: $this->_authentication->authenticate();
                $route = $this->_router->match($this->_request);

                // check if the operation is a secure operation & whether the request was made in HTTPS
                if ($route->isSecure() && !$this->_request->isSecure()) {
                    throw new Mage_Webapi_Exception(
                        $this->_helper->__('Operation allowed only in HTTPS'),
                        Mage_Webapi_Exception::HTTP_BAD_REQUEST
                    );
                }
                /** @var Mage_Webapi_Controller_Rest_Presentation $inputData */
                $inputData = $this->_restPresentation->getRequestData();
                // TODO: $this->_authorization->checkResourceAcl($route->getServiceId(), $route->getServiceMethod());
                $serviceMethod = $route->getServiceMethod();
                $service = $this->_objectManager->get($route->getServiceId());
                $outputData = $service->$serviceMethod($inputData);
                if (!is_array($outputData)) {
                    throw new LogicException(
                        $this->_helper->__('The method "%s" of service "%s" must return an array.', $serviceMethod,
                            $route->getServiceId())
                    );
                }
                $this->_restPresentation->prepareResponse($outputData);
            } catch (Exception $e) {
                $this->_response->setException($e);
            }
        }
        $this->_response->sendResponse();
        return $this;
    }
}
