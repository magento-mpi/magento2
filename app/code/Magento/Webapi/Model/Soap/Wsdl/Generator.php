<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Wsdl;

use Magento\Webapi\Model\Soap\Wsdl;
use Magento\Webapi\Model\Soap\Fault;

/**
 * WSDL generator.
 *
 * TODO: Remove warnings suppression
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class Generator
{
    const WSDL_NAME = 'MagentoWSDL';
    const WSDL_CACHE_ID = 'WSDL';

    /**
     * WSDL factory instance.
     *
     * @var \Magento\Webapi\Model\Soap\Wsdl\Factory
     */
    protected $_wsdlFactory;

    /**
     * @var \Magento\Webapi\Model\Cache\Type
     */
    protected $_cache;

    /**
     * @var \Magento\Webapi\Model\Soap\Config
     */
    protected $_apiConfig;

    /**
     * The list of registered complex types.
     *
     * @var string[]
     */
    protected $_registeredTypes = array();

    /** @var \Magento\Webapi\Helper\Config */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Soap\Config $apiConfig
     * @param \Magento\Webapi\Model\Soap\Wsdl\Factory $wsdlFactory
     * @param \Magento\Webapi\Model\Cache\Type $cache
     * @param \Magento\Webapi\Helper\Config $helper
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Config $apiConfig,
        \Magento\Webapi\Model\Soap\Wsdl\Factory $wsdlFactory,
        \Magento\Webapi\Model\Cache\Type $cache,
        \Magento\Webapi\Helper\Config $helper
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_wsdlFactory = $wsdlFactory;
        $this->_cache = $cache;
        $this->_helper = $helper;
    }

    /**
     * Generate WSDL file based on requested services (uses cache)
     *
     * @param array $requestedServices
     * @param string $endPointUrl
     * @return string
     * @throws \Exception
     */
    public function generate($requestedServices, $endPointUrl)
    {
        /** Sort requested services by names to prevent caching of the same wsdl file more than once. */
        ksort($requestedServices);
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedServices));
        $cachedWsdlContent = $this->_cache->load($cacheId);
        if ($cachedWsdlContent !== false) {
            return $cachedWsdlContent;
        }
        $services = array();
        foreach ($requestedServices as $serviceName) {
            $services[$serviceName] = $this->_apiConfig->getServiceMetadata($serviceName);
        }

        $wsdlContent = $this->_generate($services, $endPointUrl);
        $this->_cache->save($wsdlContent, $cacheId, array(\Magento\Webapi\Model\Cache\Type::CACHE_TAG));

        return $wsdlContent;
    }

    /**
     * Generate WSDL file based on requested services.
     *
     * @param array $requestedServices
     * @param string $endPointUrl
     * @return string
     * @throws \Magento\Webapi\Exception
     */
    protected function _generate($requestedServices, $endPointUrl)
    {
        $this->_collectCallInfo($requestedServices);
        $wsdl = $this->_wsdlFactory->create(self::WSDL_NAME, $endPointUrl);
        $wsdl->addSchemaTypeSection();
        // TODO: Process SOAP faults
        // $this->_addDefaultFaultComplexTypeNodes($wsdl);
        foreach ($requestedServices as $serviceClass => $serviceData) {
            $portTypeName = $this->getPortTypeName($serviceClass);
            $bindingName = $this->getBindingName($serviceClass);
            $portType = $wsdl->addPortType($portTypeName);
            $binding = $wsdl->addBinding($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName);
            $wsdl->addSoapBinding($binding, 'document', 'http://schemas.xmlsoap.org/soap/http', SOAP_1_2);
            $portName = $this->getPortName($serviceClass);
            $serviceName = $this->getServiceName($serviceClass);
            $wsdl->addService($serviceName, $portName, Wsdl::TYPES_NS
                . ':' . $bindingName, $endPointUrl, SOAP_1_2);

            foreach ($serviceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($serviceClass, $methodName);
                $inputBinding = array('use' => 'literal');
                $inputMessageName = $this->_createOperationInput($wsdl, $operationName, $methodData);

                $outputMessageName = false;
                $outputBinding = false;
                if (isset($methodData['interface']['out']['parameters'])) {
                    $outputBinding = $inputBinding;
                    $outputMessageName = $this->_createOperationOutput($wsdl, $operationName, $methodData);
                }

                /** Default SOAP fault should be added to each operation declaration */
                $faultsInfo = false;
                /*
                TODO: Process SOAP faults
                $faultsInfo = array(
                    array(
                        'name' => Fault::NODE_DETAIL_WRAPPER,
                        'message' => Wsdl::TYPES_NS . ':' . $this->_getDefaultFaultMessageName()
                    )
                );
                if (isset($methodData['interface']['faultComplexTypes'])) {
                    $faultsInfo = array_merge(
                        $faultsInfo,
                        $this->_createOperationFaults($wsdl, $operationName, $methodData)
                    );
                }
                */

                $wsdl->addPortOperation(
                    $portType,
                    $operationName,
                    $inputMessageName,
                    $outputMessageName,
                    $faultsInfo
                );
                $bindingOperation = $wsdl->addBindingOperation(
                    $binding,
                    $operationName,
                    $inputBinding,
                    $outputBinding,
                    $faultsInfo,
                    SOAP_1_2
                );
                $wsdl->addSoapOperation($bindingOperation, $operationName, SOAP_1_2);
            }
        }
        return $wsdl->toXML();
    }

    /**
     * Create an array of items that contain information about method faults.
     *
     * @param Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return array array(array('name' => ..., 'message' => ...))
     */
    protected function _createOperationFaults(Wsdl $wsdl, $operationName, $methodData)
    {
        $faults = array();
        /*
        TODO: Re-implement
        if (isset($methodData['interface']['faultComplexTypes'])) {
            foreach ($methodData['interface']['faultComplexTypes'] as $faultName => $faultComplexTypes) {
                $faultMessageName = $this->getFaultMessageName($operationName, $faultName);
                $wsdl->addElement(
                    array(
                        'name' => $faultMessageName,
                        'type' => Wsdl::TYPES_NS . ':' . $faultMessageName
                    )
                );
                foreach ($faultComplexTypes as $complexTypeNode) {
                    $wsdl->addComplexType($complexTypeNode);
                }
                $wsdl->addMessage(
                    $faultMessageName,
                    array(
                        'messageParameters' => array(
                            'element' => Wsdl::TYPES_NS . ':' . $faultMessageName
                        )
                    )
                );
                $faults[] = array(
                    'name' => $operationName . $faultName,
                    'message' => Wsdl::TYPES_NS . ':' . $faultMessageName
                );
            }
        }
        */
        return $faults;
    }



    /**
     * Create input message and corresponding element and complex types in WSDL.
     *
     * @param Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string input message name
     */
    protected function _createOperationInput(Wsdl $wsdl, $operationName, $methodData)
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
     * @param Wsdl $wsdl
     * @param string $operationName
     * @param array $methodData
     * @return string output message name
     */
    protected function _createOperationOutput(Wsdl $wsdl, $operationName, $methodData)
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
     * Get name for service portType node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getPortTypeName($serviceName)
    {
        return $serviceName . 'PortType';
    }

    /**
     * Get name for service binding node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getBindingName($serviceName)
    {
        return $serviceName . 'Binding';
    }

    /**
     * Get name for service port node.
     *
     * @param string $serviceName
     * @return string
     */
    public function getPortName($serviceName)
    {
        return $serviceName . 'Port';
    }

    /**
     * Get name for service service.
     *
     * @param string $serviceName
     * @return string
     */
    public function getServiceName($serviceName)
    {
        return $serviceName . 'Service';
    }

    /**
     * Get name of operation based on service and method names.
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
     * Walks through all requested services and checks all methods 'in' and 'out' parameters.
     *
     * @param array $requestedServices
     */
    protected function _collectCallInfo($requestedServices)
    {
        foreach ($requestedServices as $serviceName => $serviceData) {
            foreach ($serviceData['methods'] as $methodName => $methodData) {
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
                    $this->_apiConfig->setTypeData($parameterType, array('callInfo' => $callInfo));
                }
            }
        }
    }

    // TODO: Reconsider faults implementation and necessity of methods below
    /**
     * Get fault message node name for operation.
     *
     * @param string $operationName
     * @param string $faultName
     * @return string
     */
    public function getFaultMessageName($operationName, $faultName)
    {
        return $operationName . $faultName . 'Fault';
    }

    /**
     * Get info about complex types defined in the XSD for the service method faults.
     *
     * @param string $serviceMethod
     * @param \DOMDocument $domDocument
     * @return array array(array('complexTypeName' => ..., 'faultName' => ...))
     */
    public function getXsdFaultTypeNames($serviceMethod, $domDocument)
    {
        $faultTypeNames = array();
        $xpath = new \DOMXPath($domDocument);
        $serviceMethod = ucfirst($serviceMethod);
        $typeXPath = "//xsd:complexType[starts-with(@name,'{$serviceMethod}') and contains(@name,'Fault')]";
        $complexTypeNodes = $xpath->query($typeXPath);
        /** @var \DOMElement $complexTypeNode */
        foreach ($complexTypeNodes as $complexTypeNode) {
            $complexTypeName = $complexTypeNode->getAttribute('name');
            if (preg_match("/^{$serviceMethod}(\w+)Fault$/", $complexTypeName, $matches)) {
                $faultTypeNames[] = array('complexTypeName' => $complexTypeName, 'faultName' => $matches[1]);
            }
        }
        return $faultTypeNames;
    }

    /**
     * Add WSDL elements related to default SOAP fault, which are common for all operations: element, type and message.
     *
     * @param Wsdl $wsdl
     * @return \DOMNode[]
     */
    protected function _addDefaultFaultComplexTypeNodes($wsdl)
    {
        /*
        TODO: Re-implement
        $domDocument = new \DOMDocument();
        $typeName = Fault::NODE_DETAIL_WRAPPER;
        $defaultFault = $this->_generateEmptyComplexType($typeName, $domDocument);
        $elementName = Fault::NODE_DETAIL_WRAPPER;
        $wsdl->addElement(array('name' => $elementName, 'type' => Wsdl::TYPES_NS . ':' . $typeName));
        $wsdl->addMessage(
            $this->_getDefaultFaultMessageName(),
            array('messageParameters' => array('element' => Wsdl::TYPES_NS . ':' . $elementName))
        );
        $this->_addDefaultFaultElements($defaultFault);
        $wsdl->addComplexType($defaultFault);
        */
    }

    /**
     * Generate all necessary complex types for the fault of specified type.
     *
     * @param string $serviceName
     * @param string $typeName
     * @param \DOMDocument $domDocument
     * @return \DOMNode[]
     */
    protected function _getFaultComplexTypeNodes($serviceName, $typeName, $domDocument)
    {
        $complexTypesNodes = $this->getComplexTypeNodes($serviceName, $typeName, $domDocument);
        $faultTypeName = $serviceName . $typeName;
        $paramsTypeName = $faultTypeName . 'Params';
        if (isset($complexTypesNodes[$faultTypeName])) {
            /** Rename fault complex type to fault param complex type */
            $faultComplexType = $complexTypesNodes[$faultTypeName];
            $faultComplexType->setAttribute('name', $paramsTypeName);
            $complexTypesNodes[$paramsTypeName] = $complexTypesNodes[$faultTypeName];

            /** Create new fault complex type, which will contain reference to fault param complex type */
            $newFaultComplexType = $this->_generateEmptyComplexType($faultTypeName, $domDocument);
            $this->_addDefaultFaultElements($newFaultComplexType);
            /** Create 'Parameters' element and use fault param complex type as its type */
            $parametersElement = $domDocument->createElement('xsd:element');
            $parametersElement->setAttribute('name', Fault::NODE_DETAIL_PARAMETERS);
            $parametersElement->setAttribute('type', Wsdl::TYPES_NS . ':' . $paramsTypeName);
            $newFaultComplexType->firstChild->appendChild($parametersElement);

            $complexTypesNodes[$faultTypeName] = $newFaultComplexType;
        }
        return $complexTypesNodes;
    }

    /**
     * Generate empty complex type with the specified name.
     *
     * @param string $complexTypeName
     * @param \DOMDocument $domDocument
     * @return \DOMElement
     */
    protected function _generateEmptyComplexType($complexTypeName, $domDocument)
    {
        $complexTypeNode = $domDocument->createElement('xsd:complexType');
        $complexTypeNode->setAttribute('name', $complexTypeName);
        $xsdNamespace = 'http://www.w3.org/2001/XMLSchema';
        $complexTypeNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsd', $xsdNamespace);
        $domDocument->appendChild($complexTypeNode);
        $sequenceNode = $domDocument->createElement('xsd:sequence');
        $complexTypeNode->appendChild($sequenceNode);
        return $complexTypeNode;
    }

    /**
     * Add 'Detail' and 'Trace' elements to the fault element.
     *
     * @param \DOMElement $faultElement
     */
    protected function _addDefaultFaultElements($faultElement)
    {
        /** Create 'Code' element */
        $codeElement = $faultElement->ownerDocument->createElement('xsd:element');
        $codeElement->setAttribute('name', Fault::NODE_DETAIL_CODE);
        $codeElement->setAttribute('type', 'xsd:int');
        $faultElement->firstChild->appendChild($codeElement);

        /** Create 'Trace' element */
        $traceElement = $faultElement->ownerDocument->createElement('xsd:element');
        $traceElement->setAttribute('name', Fault::NODE_DETAIL_TRACE);
        $traceElement->setAttribute('type', 'xsd:string');
        $traceElement->setAttribute('minOccurs', '0');
        $faultElement->firstChild->appendChild($traceElement);
    }

    /**
     * Retrieve name of default SOAP fault message name in WSDL.
     *
     * @return string
     */
    protected function _getDefaultFaultMessageName()
    {
        return Fault::NODE_DETAIL_WRAPPER;
    }
}
