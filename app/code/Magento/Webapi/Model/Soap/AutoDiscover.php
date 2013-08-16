<?php
use Zend\Soap\Wsdl;

/**
 * Auto discovery tool for WSDL generation from Magento web API configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_AutoDiscover
{
    const WSDL_NAME = 'MagentoWSDL';

    /**
     * Cache ID for generated WSDL content.
     */
    const WSDL_CACHE_ID = 'WSDL';

    /**
     * API Resource config instance.
     * Used to retrieve complex types data.
     *
     * @var Magento_Webapi_Model_Config_Soap
     */
    protected $_apiConfig;

    /**
     * WSDL factory instance.
     *
     * @var Magento_Webapi_Model_Soap_Wsdl_Factory
     */
    protected $_wsdlFactory;

    /**
     * @var Magento_Webapi_Helper_Config
     */
    protected $_helper;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * Construct auto discover with resource config and list of requested resources.
     *
     * @param Magento_Webapi_Model_Config_Soap $apiConfig
     * @param Magento_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory
     * @param Magento_Webapi_Helper_Config $helper
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     */
    public function __construct(
        Magento_Webapi_Model_Config_Soap $apiConfig,
        Magento_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory,
        Magento_Webapi_Helper_Config $helper,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Cache_StateInterface $cacheState
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_wsdlFactory = $wsdlFactory;
        $this->_helper = $helper;
        $this->_cache = $cache;
        $this->_cacheState = $cacheState;
    }

    /**
     * Generate WSDL content and save it to cache.
     *
     * @param array $requestedResources
     * @param string $endpointUrl
     * @return string
     * @throws Magento_Webapi_Exception
     */
    public function handle($requestedResources, $endpointUrl)
    {
        /** Sort requested resources by names to prevent caching of the same wsdl file more than once. */
        ksort($requestedResources);
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedResources));
        if ($this->_cacheState->isEnabled(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = $this->_cache->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        $resources = array();
        try {
            foreach ($requestedResources as $resourceName => $resourceVersion) {
                $resources[$resourceName] = $this->_apiConfig->getResourceDataMerged($resourceName, $resourceVersion);
            }
        } catch (Exception $e) {
            throw new Magento_Webapi_Exception($e->getMessage(), Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $wsdlContent = $this->generate($resources, $endpointUrl);

        if ($this->_cacheState->isEnabled(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache
                 ->save($wsdlContent, $cacheId, array(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Generate WSDL file based on requested resources.
     *
     * @param array $requestedResources
     * @param string $endPointUrl
     * @return string
     */
    public function generate($requestedResources, $endPointUrl)
    {
        $this->_collectCallInfo($requestedResources);
        $wsdl = $this->_wsdlFactory->create(self::WSDL_NAME, $endPointUrl);
        $wsdl->addSchemaTypeSection();

        foreach ($requestedResources as $resourceName => $resourceData) {
            $portTypeName = $this->getPortTypeName($resourceName);
            $bindingName = $this->getBindingName($resourceName);
            $portType = $wsdl->addPortType($portTypeName);
            $binding = $wsdl->addBinding($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName);
            $wsdl->addSoapBinding($binding, 'document', 'http://schemas.xmlsoap.org/soap/http', SOAP_1_2);
            $portName = $this->getPortName($resourceName);
            $serviceName = $this->getServiceName($resourceName);
            $wsdl->addService($serviceName, $portName, 'tns:' . $bindingName, $endPointUrl, SOAP_1_2);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($resourceName, $methodName);
                $inputBinding = array('use' => 'literal');
                $inputMessageName = $this->_createOperationInput($wsdl, $operationName, $methodData);

                $outputMessageName = false;
                $outputBinding = false;
                if (isset($methodData['interface']['out']['parameters'])) {
                    $outputBinding = $inputBinding;
                    $outputMessageName = $this->_createOperationOutput($wsdl, $operationName, $methodData);
                }

                $wsdl->addPortOperation($portType, $operationName, $inputMessageName, $outputMessageName);
                $bindingOperation = $wsdl->addBindingOperation(
                    $binding,
                    $operationName,
                    $inputBinding,
                    $outputBinding,
                    false,
                    SOAP_1_2
                );
                $wsdl->addSoapOperation($bindingOperation, $operationName, SOAP_1_2);
                // @TODO: implement faults binding
            }
        }

        return $wsdl->toXML();
    }

    /**
     * Create input message and corresponding element and complex types in WSDL.
     *
     * @param Magento_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string input message name
     */
    protected function _createOperationInput(Magento_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
    {
        $inputMessageName = $this->getInputMessageName($operationName);
        $complexTypeName = $this->getElementComplexTypeName($inputMessageName);
        $inputParameters = array();
        $elementData = array(
            'name' => $inputMessageName,
            'type' => Wsdl::TYPES_NS . ':' . $complexTypeName
        );
        if (isset($methodData['interface']['in']['parameters'])) {
            $inputParameters = $methodData['interface']['in']['parameters'];
        } else {
            $elementData['nillable'] = 'true';
        }
        $wsdl->addElement($elementData);
        $callInfo = array();
        $callInfo['requiredInput']['yes']['calls'] = array($operationName);
        $typeData = array(
            'documentation' => $methodData['documentation'],
            'parameters' => $inputParameters,
            'callInfo' => $callInfo,
        );
        $this->_apiConfig->setTypeData($complexTypeName, $typeData);
        $wsdl->addComplexType($complexTypeName);
        $wsdl->addMessage(
            $inputMessageName,
            array(
                'messageParameters' => array(
                    'element' => Wsdl::TYPES_NS . ':' . $inputMessageName
                )
            )
        );

        return Wsdl::TYPES_NS . ':' . $inputMessageName;
    }

    /**
     * Create output message and corresponding element and complex types in WSDL.
     *
     * @param Magento_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string output message name
     */
    protected function _createOperationOutput(Magento_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
    {
        $outputMessageName = $this->getOutputMessageName($operationName);
        $complexTypeName = $this->getElementComplexTypeName($outputMessageName);
        $wsdl->addElement(
            array(
                'name' => $outputMessageName,
                'type' => Wsdl::TYPES_NS . ':' . $complexTypeName
            )
        );
        $callInfo = array();
        $callInfo['returned']['always']['calls'] = array($operationName);
        $typeData = array(
            'documentation' => sprintf('Response container for the %s call.', $operationName),
            'parameters' => $methodData['interface']['out']['parameters'],
            'callInfo' => $callInfo,
        );
        $this->_apiConfig->setTypeData($complexTypeName, $typeData);
        $wsdl->addComplexType($complexTypeName);
        $wsdl->addMessage(
            $outputMessageName,
            array(
                'messageParameters' => array(
                    'element' => Wsdl::TYPES_NS . ':' . $outputMessageName
                )
            )
        );

        return Wsdl::TYPES_NS . ':' . $outputMessageName;
    }

    /**
     * Get name of complexType for message element.
     *
     * @param string $messageName
     * @return string
     */
    public function getElementComplexTypeName($messageName)
    {
        return ucfirst($messageName);
    }

    /**
     * Get name for resource portType node.
     *
     * @param string $resourceName
     * @return string
     */
    public function getPortTypeName($resourceName)
    {
        return $resourceName . 'PortType';
    }

    /**
     * Get name for resource binding node.
     *
     * @param string $resourceName
     * @return string
     */
    public function getBindingName($resourceName)
    {
        return $resourceName . 'Binding';
    }

    /**
     * Get name for resource port node.
     *
     * @param string $resourceName
     * @return string
     */
    public function getPortName($resourceName)
    {
        return $resourceName . 'Port';
    }

    /**
     * Get name for resource service.
     *
     * @param string $resourceName
     * @return string
     */
    public function getServiceName($resourceName)
    {
        return $resourceName . 'Service';
    }

    /**
     * Get name of operation based on resource and method names.
     *
     * @param string $resourceName
     * @param string $methodName
     * @return string
     */
    public function getOperationName($resourceName, $methodName)
    {
        return $resourceName . ucfirst($methodName);
    }

    /**
     * Get input message node name for operation.
     *
     * @param string $operationName
     * @return string
     */
    public function getInputMessageName($operationName)
    {
        return $operationName . 'Request';
    }

    /**
     * Get output message node name for operation.
     *
     * @param string $operationName
     * @return string
     */
    public function getOutputMessageName($operationName)
    {
        return $operationName . 'Response';
    }

    /**
     * Collect data about complex types call info.
     * Walks through all requested resources and checks all methods 'in' and 'out' parameters.
     *
     * @param array $requestedResources
     */
    protected function _collectCallInfo($requestedResources)
    {
        foreach ($requestedResources as $resourceName => $resourceData) {
            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $this->_processInterfaceCallInfo($methodData['interface'], $resourceName, $methodName);
            }
        }
    }

    /**
     * Process call info data from interface.
     *
     * @param array $interface
     * @param string $resourceName
     * @param string $methodName
     */
    protected function _processInterfaceCallInfo($interface, $resourceName, $methodName)
    {
        foreach ($interface as $direction => $interfaceData) {
            $direction = ($direction == 'in') ? 'requiredInput' : 'returned';
            foreach ($interfaceData['parameters'] as $parameterData) {
                $parameterType = $parameterData['type'];
                if (!$this->_helper->isTypeSimple($parameterType)) {
                    $operation = $this->getOperationName($resourceName, $methodName);
                    if ($parameterData['required']) {
                        $condition = ($direction == 'requiredInput') ? 'yes' : 'always';
                    } else {
                        $condition = ($direction == 'requiredInput') ? 'no' : 'conditionally';
                    }
                    $callInfo = array();
                    $callInfo[$direction][$condition]['calls'][] = $operation;
                    $this->_apiConfig->setTypeData($parameterType, array('callInfo' => $callInfo));
                }
            }
        }
    }

}
