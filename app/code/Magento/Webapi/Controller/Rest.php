<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Controller;

use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationService;
use Magento\Webapi\Controller\Rest\Router\Route;
use Magento\Service\Entity\MagentoDtoInterface;

/**
 * Front controller for WebAPI REST area.
 *
 * TODO: Consider warnings suppression removal
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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

    /** @var \Magento\Oauth\OauthInterface */
    protected $_oauthService;

    /** @var  \Magento\Oauth\Helper\Request */
    protected $_oauthHelper;

    /** @var AuthorizationService */
    protected $_authorizationService;

    /** @var ServiceArgsSerializer */
    protected $_serializer;

    /** @var \Magento\Webapi\Controller\ErrorProcessor */
    protected $_errorProcessor;

    /**
     * Initialize dependencies.
     *
     * TODO: Consider removal of warning suppression
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param Rest\Request $request
     * @param Rest\Response $response
     * @param Rest\Router $router
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\State $appState
     * @param \Magento\Oauth\OauthInterface $oauthService
     * @param \Magento\Oauth\Helper\Request $oauthHelper
     * @param AuthorizationService $authorizationService
     * @param ServiceArgsSerializer $serializer
     * @param \Magento\Webapi\Controller\ErrorProcessor $errorProcessor
     */
    public function __construct(
        \Magento\Webapi\Controller\Rest\Request $request,
        \Magento\Webapi\Controller\Rest\Response $response,
        \Magento\Webapi\Controller\Rest\Router $router,
        \Magento\ObjectManager $objectManager,
        \Magento\App\State $appState,
        \Magento\Oauth\OauthInterface $oauthService,
        \Magento\Oauth\Helper\Request $oauthHelper,
        AuthorizationService $authorizationService,
        ServiceArgsSerializer $serializer,
        \Magento\Webapi\Controller\ErrorProcessor $errorProcessor
    ) {
        $this->_router = $router;
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        $this->_oauthService = $oauthService;
        $this->_oauthHelper = $oauthHelper;
        $this->_authorizationService = $authorizationService;
        $this->_serializer = $serializer;
        $this->_errorProcessor = $errorProcessor;
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
            $oauthRequest = $this->_oauthHelper->prepareRequest($this->_request);
            $consumerId = $this->_oauthService->validateAccessTokenRequest(
                $oauthRequest,
                $this->_oauthHelper->getRequestUrl($this->_request),
                $this->_request->getMethod()
            );
            $this->_request->setConsumerId($consumerId);
            $route = $this->_router->match($this->_request);

            if (!$this->_authorizationService->isAllowed($route->getAclResources())) {
                // TODO: Consider passing Integration ID instead of Consumer ID
                throw new \Magento\Service\AuthorizationException(
                    "Not Authorized.",
                    0,
                    null,
                    array(),
                    'authorization',
                    "Consumer ID = {$consumerId}",
                    implode($route->getAclResources(), ', '));
            }

            if ($route->isSecure() && !$this->_request->isSecure()) {
                throw new \Magento\Webapi\Exception(__('Operation allowed only in HTTPS'));
            }
            /** @var array $inputData */
            $inputData = $this->_request->getRequestData();
            $serviceMethodName = $route->getServiceMethod();
            $serviceClassName = $route->getServiceClass();
            $inputParams = $this->_serializer->getInputData($serviceClassName, $serviceMethodName, $inputData);
            $service = $this->_objectManager->get($serviceClassName);
            /** @var \Magento\Service\Entity\AbstractDto $outputData */
            $outputData = call_user_func_array([$service, $serviceMethodName], $inputParams);
            $outputArray = $this->_getOutputArray($outputData);
            $this->_response->prepareResponse($outputArray);
        } catch (\Exception $e) {
            $maskedException = $this->_errorProcessor->maskException($e);
            $this->_response->setException($maskedException);
        }
        return $this->_response;
    }

    /**
     * Converts the incoming data into an array format.
     *
     * If the data provided is null, then an empty array is returned.  Otherwise, if the data is an object, it is
     * assumed to be a DTO and converted to an associative array with keys representing the properties of the DTO.
     * Nested DTOs are also converted.  If the data provided is itself an array, then we iterate through the contents
     * and convert each piece individually.
     *
     * @param array|\Magento\Service\Entity\MagentoDtoInterface $data A DTO or an array of DTOs to be converted into
     *                                                                a key-value array format.
     * @return array
     */
    protected function _getOutputArray($data)
    {
        if (!is_null($data)) {
            $outputArray = [];
            if (is_array($data)) {
                foreach ($data as $datum) {
                    if (method_exists($datum, '__toArray')) {
                        $outputArray[] = $datum->__toArray();
                    } else {
                        $outputArray[] = $datum;
                    }
                }
            } else {
                /** @var MagentoDtoInterface $data */
                $outputArray = $data->__toArray();
            }
            return $outputArray;
        }
        return null;
    }

}
