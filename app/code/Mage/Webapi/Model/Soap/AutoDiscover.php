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
class Mage_Webapi_Model_Soap_AutoDiscover
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
     * @var Mage_Core_Service_Config
     */
    protected $_serviceConfig;

    /**
     * WSDL factory instance.
     *
     * @var Mage_Webapi_Model_Soap_Wsdl_Factory
     */
    protected $_wsdlFactory;

    /**
     * @var Mage_Webapi_Helper_Config
     */
    protected $_helper;

    /** @var Mage_Core_Model_CacheInterface */
    protected $_cache;

    /**
     * Construct auto discover with resource config and list of requested resources.
     *
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Mage_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Model_CacheInterface $cache
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Core_Service_Config $serviceConfig,
        Mage_Webapi_Model_Soap_Wsdl_Factory $wsdlFactory,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_CacheInterface $cache
    ) {
        $this->_serviceConfig = $serviceConfig;
        $this->_wsdlFactory = $wsdlFactory;
        $this->_helper = $helper;
        $this->_cache = $cache;
    }

    /**
     * Generate WSDL content and save it to cache.
     *
     * @param array $requestedResources
     * @param string $endpointUrl
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function handle($requestedResources, $endpointUrl)
    {
        /** Sort requested resources by names to prevent caching of the same wsdl file more than once. */
        ksort($requestedResources);
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedResources));
        if ($this->_cache->canUse(Mage_Core_Service_Config::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = $this->_cache->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        $resources = array();
        try {
            foreach ($requestedResources as $serviceName => $resourceVersion) {
                $resources[$serviceName] = $this->_serviceConfig->getServiceData($serviceName);
            }
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $wsdlContent = $this->generate($resources, $endpointUrl);

        if ($this->_cache->canUse(Mage_Core_Service_Config::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save($wsdlContent, $cacheId, array(Mage_Core_Service_Config::WEBSERVICE_CACHE_TAG));
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

        foreach ($requestedResources as $serviceName => $resourceData) {
            $portTypeName = $this->getPortTypeName($serviceName);
            $bindingName = $this->getBindingName($serviceName);
            $portType = $wsdl->addPortType($portTypeName);
            $binding = $wsdl->addBinding($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName);
            $wsdl->addSoapBinding($binding, 'document', 'http://schemas.xmlsoap.org/soap/http', SOAP_1_2);
            $portName = $this->getPortName($serviceName);
            $wsdlServiceName = $this->getServiceName($serviceName);
            $wsdl->addService($wsdlServiceName, $portName, 'tns:' . $bindingName, $endPointUrl, SOAP_1_2);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($serviceName, $methodName);
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
     * @param Mage_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string input message name
     */
    protected function _createOperationInput(Mage_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
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
        $this->_serviceConfig->setTypeData($complexTypeName, $typeData);
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
     * @param Mage_Webapi_Model_Soap_Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string output message name
     */
    protected function _createOperationOutput(Mage_Webapi_Model_Soap_Wsdl $wsdl, $operationName, $methodData)
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
        $this->_serviceConfig->setTypeData($complexTypeName, $typeData);
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
     * @param string $serviceName
     * @return string
     */
    public function getPortTypeName($serviceName)
    {
        return $serviceName . 'PortType';
    }

    /**
     * Get name for resource binding node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getBindingName($serviceName)
    {
        return $serviceName . 'Binding';
    }

    /**
     * Get name for resource port node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getPortName($serviceName)
    {
        return $serviceName . 'Port';
    }

    /**
     * Get name for resource service.
     *
     * @param string $serviceName
     * @return string
     */
    public function getServiceName($serviceName)
    {
        return $serviceName . 'Service';
    }

    /**
     * Get name of operation based on resource and method names.
     *
     * @param string $serviceName
     * @param string $methodName
     * @return string
     */
    public function getOperationName($serviceName, $methodName)
    {
        return $serviceName . ucfirst($methodName);
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
        foreach ($requestedResources as $serviceName => $resourceData) {
            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $this->_processInterfaceCallInfo($methodData['interface'], $serviceName, $methodName);
            }
        }
    }

    /**
     * Process call info data from interface.
     *
     * @param array $interface
     * @param string $serviceName
     * @param string $methodName
     */
    protected function _processInterfaceCallInfo($interface, $serviceName, $methodName)
    {
        foreach ($interface as $direction => $interfaceData) {
            $direction = ($direction == 'in') ? 'requiredInput' : 'returned';
            foreach ($interfaceData['parameters'] as $parameterData) {
                $parameterType = $parameterData['type'];
                if (!$this->_helper->isTypeSimple($parameterType)) {
                    $operation = $this->getOperationName($serviceName, $methodName);
                    if ($parameterData['required']) {
                        $condition = ($direction == 'requiredInput') ? 'yes' : 'always';
                    } else {
                        $condition = ($direction == 'requiredInput') ? 'no' : 'conditionally';
                    }
                    $callInfo = array();
                    $callInfo[$direction][$condition]['calls'][] = $operation;
                    $this->_serviceConfig->setTypeData($parameterType, array('callInfo' => $callInfo));
                }
            }
        }
    }

}
