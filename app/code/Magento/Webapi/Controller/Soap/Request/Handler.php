<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Soap\Request;

use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationService;
use Magento\Service\Data\AbstractObject;
use Magento\Webapi\Model\Soap\Config as SoapConfig;
use Magento\Webapi\Controller\Soap\Request as SoapRequest;
use Magento\Webapi\Exception as WebapiException;
use Magento\Webapi\ServiceAuthorizationException;
use Magento\Webapi\Controller\ServiceArgsSerializer;

/**
 * Handler of requests to SOAP server.
 *
 * The main responsibility is to instantiate proper action controller (service) and execute requested method on it.
 *
 * TODO: Fix warnings suppression
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Handler
{
    const RESULT_NODE_NAME = 'result';

    /** @var SoapRequest */
    protected $_request;

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var SoapConfig */
    protected $_apiConfig;

    /** @var AuthorizationService */
    protected $_authorizationService;

    /** @var \Magento\Webapi\Helper\Data */
    protected $_helper;

    /** @var ServiceArgsSerializer */
    protected $_serializer;

    /**
     * Initialize dependencies.
     *
     * @param SoapRequest $request
     * @param \Magento\ObjectManager $objectManager
     * @param SoapConfig $apiConfig
     * @param AuthorizationService $authorizationService
     * @param \Magento\Webapi\Helper\Data $helper
     * @param ServiceArgsSerializer $serializer
     */
    public function __construct(
        SoapRequest $request,
        \Magento\ObjectManager $objectManager,
        SoapConfig $apiConfig,
        AuthorizationService $authorizationService,
        \Magento\Webapi\Helper\Data $helper,
        ServiceArgsSerializer $serializer
    ) {
        $this->_request = $request;
        $this->_objectManager = $objectManager;
        $this->_apiConfig = $apiConfig;
        $this->_authorizationService = $authorizationService;
        $this->_helper = $helper;
        $this->_serializer = $serializer;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     * @return \stdClass|null
     * @throws WebapiException
     * @throws \LogicException
     * @throws ServiceAuthorizationException
     */
    public function __call($operation, $arguments)
    {
        $requestedServices = $this->_request->getRequestedServices();
        $serviceMethodInfo = $this->_apiConfig->getServiceMethodInfo($operation, $requestedServices);
        $serviceClass = $serviceMethodInfo[SoapConfig::KEY_CLASS];
        $serviceMethod = $serviceMethodInfo[SoapConfig::KEY_METHOD];

        // check if the operation is a secure operation & whether the request was made in HTTPS
        if ($serviceMethodInfo[SoapConfig::KEY_IS_SECURE] && !$this->_request->isSecure()) {
            throw new WebapiException(__("Operation allowed only in HTTPS"));
        }

        if (!$this->_authorizationService->isAllowed($serviceMethodInfo[SoapConfig::KEY_ACL_RESOURCES])) {
            // TODO: Consider passing Integration ID instead of Consumer ID
            throw new ServiceAuthorizationException(
                "Not Authorized.",
                0,
                null,
                array(),
                'authorization',
                "Consumer ID = {$this->_request->getConsumerId()}",
                implode($serviceMethodInfo[SoapConfig::KEY_ACL_RESOURCES], ', '));
        }
        $service = $this->_objectManager->get($serviceClass);
        $inputData = $this->_prepareRequestData($serviceClass, $serviceMethod, $arguments);
        $outputData = call_user_func_array(array($service, $serviceMethod), $inputData);
        return $this->_prepareResponseData($outputData);
    }

    /**
     * Convert SOAP operation arguments into format acceptable by service method.
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param array $arguments
     * @return array
     */
    protected function _prepareRequestData($serviceClass, $serviceMethod, $arguments)
    {
        /** SoapServer wraps parameters into array. Thus this wrapping should be removed to get access to parameters. */
        $arguments = reset($arguments);
        $arguments = $this->_helper->_toArray($arguments);
        return $this->_serializer->getInputData($serviceClass, $serviceMethod, $arguments);
    }

    /**
     * Convert service response into format acceptable by SoapServer.
     *
     * @param object|array|string|int|float|null $data
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _prepareResponseData($data)
    {
        if ($data instanceof AbstractObject) {
            $result = $this->_helper->unpackDataObject($data);
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $result[$key] = $value instanceof AbstractObject ? $this->_helper->unpackDataObject($value) : $value;
            }
        } elseif (is_scalar($data) || is_null($data)) {
            $result = $data;
        } else {
            throw new \InvalidArgumentException("Service returned result in invalid format.");
        }
        return array(self::RESULT_NODE_NAME => $result);
    }

}
