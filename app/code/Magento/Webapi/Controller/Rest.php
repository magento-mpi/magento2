<?php
/**
 * Front controller for WebAPI REST area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class Rest implements \Magento\App\FrontControllerInterface
{
    /** @var \Magento\Webapi\Controller\Rest\Router */
    protected $_router;

    /** @var \Magento\Webapi\Controller\Rest\Request */
    protected $_request;

    /** @var \Magento\Webapi\Controller\Rest\Response */
    protected $_response;

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\App\State */
    protected $_appState;

    /** @var \Magento\Oauth\Service\OauthV1Interface */
    protected $_oauthService;

    /** @var  \Magento\Oauth\Helper\Data */
    protected $_oauthHelper;

    /**
     * @param Rest\Request $request
     * @param Rest\Response $response
     * @param Rest\Router $router
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\State $appState
     * @param \Magento\Oauth\Service\OauthV1Interface $oauthService
     * @param \Magento\Oauth\Helper\Data $oauthHelper
     */
    public function __construct(
        \Magento\Webapi\Controller\Rest\Request $request,
        \Magento\Webapi\Controller\Rest\Response $response,
        \Magento\Webapi\Controller\Rest\Router $router,
        \Magento\ObjectManager $objectManager,
        \Magento\App\State $appState,
        \Magento\Oauth\Service\OauthV1Interface $oauthService,
        \Magento\Oauth\Helper\Data $oauthHelper
    ) {
        $this->_router = $router;
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        $this->_oauthService = $oauthService;
        $this->_oauthHelper = $oauthHelper;
    }

    /**
     * Initialize front controller
     *
     * @return \Magento\Webapi\Controller\Rest
     */
    public function init()
    {
        return $this;
    }

    /**
     * Handle REST request
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        $pathParts = explode('/', trim($request->getPathInfo(), '/'));
        array_shift($pathParts);
        $request->setPathInfo('/' . implode('/', $pathParts));
        try {
            if (!$this->_appState->isInstalled()) {
                throw new \Magento\Webapi\Exception(__('Magento is not yet installed'));
            }
            $oauthReq = $this->_oauthHelper->prepareServiceRequest($this->_request, $this->_request->getRequestData());
            $this->_oauthService->validateAccessTokenRequest($oauthReq);
            $route = $this->_router->match($this->_request);

            if ($route->isSecure() && !$this->_request->isSecure()) {
                throw new \Magento\Webapi\Exception(__('Operation allowed only in HTTPS'));
            }
            /** @var array $inputData */
            $inputData = $this->_request->getRequestData();
            $serviceMethod = $route->getServiceMethod();
            $service = $this->_objectManager->get($route->getServiceClass());
            $outputData = $service->$serviceMethod($inputData);
            if (!is_array($outputData)) {
                throw new \LogicException(
                    sprintf('The method "%s" of service "%s" must return an array.', $serviceMethod,
                        $route->getServiceClass())
                );
            }
            $this->_response->prepareResponse($outputData);
        } catch (\Exception $e) {
            $this->_response->setException($e);
        }
        return $this->_response;
    }
}
