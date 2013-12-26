<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Controller\Soap;

use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationService;
use Magento\Webapi\Model\Soap\Config as SoapConfig;
use Magento\Webapi\Controller\Soap\Request as SoapRequest;
use Magento\Webapi\Exception as WebapiException;
use Magento\Service\AuthorizationException;
use Zend\Code\Reflection\ClassReflection;

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

    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /**
     * Initialize dependencies.
     *
     * @param SoapRequest $request
     * @param \Magento\ObjectManager $objectManager
     * @param SoapConfig $apiConfig
     * @param AuthorizationService $authorizationService
     * @param \Magento\Webapi\Helper\Config $configHelper
     */
    public function __construct(
        SoapRequest $request,
        \Magento\ObjectManager $objectManager,
        SoapConfig $apiConfig,
        AuthorizationService $authorizationService,
        \Magento\Webapi\Helper\Config $configHelper
    ) {
        $this->_request = $request;
        $this->_objectManager = $objectManager;
        $this->_apiConfig = $apiConfig;
        $this->_authorizationService = $authorizationService;
        $this->_configHelper = $configHelper;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     * @return \stdClass|null
     * @throws WebapiException
     * @throws \LogicException
     * @throws AuthorizationException
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
            throw new AuthorizationException(
                "Not Authorized.",
                0,
                null,
                array(),
                'authorization',
                "Consumer ID = {$this->_request->getConsumerId()}",
                implode($serviceMethodInfo[SoapConfig::KEY_ACL_RESOURCES], ', '));
        }
        $service = $this->_objectManager->get($serviceClass);
        $outputData = call_user_func_array(array($service, $serviceMethod), $this->_prepareRequestData($arguments));
        return $this->_prepareResponseData($outputData);
    }

    /**
     * Convert SOAP operation arguments into format acceptable by service method.
     *
     * @param array $arguments
     * @return array
     */
    protected function _prepareRequestData($arguments)
    {
        /** SoapServer wraps parameters into array. Thus this wrapping should be removed to get access to parameters. */
        $arguments = reset($arguments);
        $this->_associativeObjectToArray($arguments);
        $arguments = get_object_vars($arguments);
        foreach ($arguments as $argument) {
            if ($this->_isDto($argument)) {
                $this->_packDto($argument);
            }
        }
        return $arguments;
    }

    /**
     * Convert service response into format acceptable by SoapServer.
     *
     * @param \Magento\Service\Entity\AbstractDto|array|string|int|double|null $data
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _prepareResponseData($data)
    {
        if ($this->_isDto($data)) {
            $this->_unpackDto($data);
        } else if (is_array($data)) {
            foreach ($data as $dataItem) {
                if ($this->_isDto($dataItem)) {
                    $this->_unpackDto($dataItem);
                }
            }
        } elseif (!(is_string($data) || is_numeric($data) || is_null($data))) {
            throw new \InvalidArgumentException("Service returned result in invalid format.");
        }
        return array('result' => $data);
    }

    /**
     * Use DTO setters to set data which was set directly to fields by SoapServer.
     *
     * This method processes all nested DTOs recursively.
     *
     * @param \Magento\Service\Entity\AbstractDto $dto DTO object is changed by reference
     * @return Handler
     */
    protected function _packDto(\Magento\Service\Entity\AbstractDto &$dto)
    {
        $fields = get_object_vars($dto);
        foreach ($fields as $fieldName => $fieldValue) {
            if ($this->_isDto($fieldValue)) {
                $this->_packDto($fieldValue);
            }
            $setterName = $this->_configHelper->dtoFieldNameToSetterName($fieldName);
            $dto->$setterName($fieldValue);
            unset($dto->$fieldName);
        }
        return $this;
    }

    /**
     * Initialize DTO public fields by data retrieved with DTO getters. Allows SoapServer to extract data from DTO.
     *
     * This method processes all nested DTOs recursively.
     *
     * @param \Magento\Service\Entity\AbstractDto $dto DTO object is changed by reference
     * @return Handler
     */
    protected function _unpackDto(\Magento\Service\Entity\AbstractDto &$dto)
    {
        // TODO: Performance impact related to Reflection usage can be avoided if DTOs store data in public fields
        $classReflection = new ClassReflection($dto);
        /** @var \Zend\Code\Reflection\MethodReflection $methodReflection */
        foreach ($classReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflection) {
            if (strpos($methodReflection->getName(), 'get') === 0) {
                $getterName = $methodReflection->getName();
                $fieldName = $this->_configHelper->dtoGetterNameToFieldName($getterName);
                $fieldValue = $dto->$getterName();
                if ($this->_isDto($fieldValue)) {
                    $this->_unpackDto($fieldValue);
                }
                $dto->$fieldName = $fieldValue;
            }
        }
        return $this;
    }

    /**
     * Check if provided variable is service DTO.
     *
     * @param mixed $var
     * @return bool
     */
    protected function _isDto($var)
    {
        return (is_object($var) && $var instanceof \Magento\Service\Entity\AbstractDto);
    }

    /**
     * Go through an object parameters and unpack associative object to array.
     *
     * This function uses recursion and operates by reference.
     *
     * @param \stdClass|array $obj
     * @return bool
     */
    protected function _associativeObjectToArray(&$obj)
    {
        if (is_object($obj)) {
            if (property_exists($obj, 'key') && property_exists($obj, 'value')) {
                if (count(array_keys(get_object_vars($obj))) === 2) {
                    $obj = array($obj->key => $obj->value);
                    return true;
                }
            } else {
                foreach (array_keys(get_object_vars($obj)) as $key) {
                    $this->_associativeObjectToArray($obj->$key);
                }
            }
        } else if (is_array($obj)) {
            $arr = array();
            $object = $obj;
            foreach ($obj as &$value) {
                if ($this->_associativeObjectToArray($value)) {
                    array_walk($value, function ($val, $key) use (&$arr) {
                        $arr[$key] = $val;
                    });
                    $object = $arr;
                }
            }
            $obj = $object;
        }
        return false;
    }
}
