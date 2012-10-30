<?php
/**
 * Auto discovery class for WSDL generation.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_AutoDiscover
{
    /**#@+
     * WSDL name and Service name attributes value
     */
    const WSDL_NAME = 'MagentoWSDL';
    const SERVICE_NAME = 'MagentoAPI';
    const ARRAY_ITEM_KEY_NAME = 'item';
    /**#@-*/

    /**
     * API Resource config instance.
     * Used to retrieve complex types data.
     *
     * @var Mage_Webapi_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * WSDL builder instance.
     *
     * @var Mage_Webapi_Model_Soap_Wsdl
     */
    protected $_wsdl;

    /**
     * Service port endpoint URL.
     *
     * @var string
     */
    protected $_endpointUrl;

    /**
     * List of already processed complex types.
     * Used to avoid cyclic recursion.
     *
     * @var array
     */
    protected $_processedTypes = array();

    /**
     * List of operations directions (requiredInput/returned) and conditions (yes/no/conditionally)
     * in which each type is called.
     *
     * @var array
     */
    protected $_typeCallInfo;

    /**
     * Construct auto discover with resource config and list of requested resources.
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        if (!isset($options['resource_config'])) {
            throw new InvalidArgumentException('"resource_config" option is required.');
        }
        if (!$options['resource_config'] instanceof Mage_Webapi_Model_Config_Resource) {
            throw new InvalidArgumentException('Invalid resource config.');
        }
        $this->_resourceConfig = $options['resource_config'];

        if (!isset($options['endpoint_url'])) {
            throw new InvalidArgumentException('"endpoint_url" option is required.');
        }
        $this->_endpointUrl = $options['endpoint_url'];

        if (isset($options['wsdl']) && $options['wsdl'] instanceof Mage_Webapi_Model_Soap_Wsdl) {
            $this->_wsdl = $options['wsdl'];
        } else {
            // TODO: Refactor according to DI
            $this->_wsdl = Mage::getModel('Mage_Webapi_Model_Soap_Wsdl', array(
                'name' => self::WSDL_NAME,
                'uri' => $options['endpoint_url'],
            ));
        }
    }

    /**
     * Generate WSDL file based on requested resources.
     *
     * @param array $requestedResources
     * @return string
     */
    public function generate($requestedResources)
    {
        $this->_collectCallInfo($requestedResources);
        $service = $this->_wsdl->addService(self::SERVICE_NAME);

        foreach ($requestedResources as $resourceName => $resourceData) {
            $portTypeName = $this->getPortTypeName($resourceName);
            $bindingName = $this->getBindingName($resourceName);
            $portType = $this->_wsdl->addPortType($portTypeName);
            $binding = $this->_wsdl->addBinding($bindingName, $portTypeName);
            $this->_wsdl->addSoapBinding($binding);
            $portName = $this->getPortName($resourceName);
            $this->_wsdl->addServicePort($service, $portName, $bindingName, $this->_endpointUrl);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($resourceName, $methodName);

                $bindingInput = array('use' => 'literal');
                $inputMessageName = $inputTypeName = $this->getInputMessageName($operationName);
                $complexTypeForElementName = ucfirst($inputTypeName);
                $inputParameters = array();
                $elementData = array(
                    'name' => $inputTypeName,
                    'type' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $complexTypeForElementName
                );
                if (isset($methodData['interface']['in']['parameters'])) {
                    $inputParameters = $methodData['interface']['in']['parameters'];
                } else {
                    $elementData['nillable'] = 'true';
                }
                $this->_wsdl->addElement($elementData);

                $callInfo = array();
                $callInfo['requiredInput']['yes']['calls'] = array($operationName);
                $this->_processComplexType($complexTypeForElementName, $inputParameters, $methodData['documentation'],
                    $callInfo);
                $this->_wsdl->addMessage($inputMessageName, array(
                    'messageParameters' => array(
                        'element' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $inputTypeName
                    )
                ));

                $outputMessageName = null;
                $bindingOutput = null;
                if (isset($methodData['interface']['out']['parameters'])) {
                    $bindingOutput = array('use' => 'literal');
                    $outputMessageName = $outputElementName = $this->getOutputMessageName($operationName);
                    $complexTypeForElementName = ucfirst($outputElementName);
                    $this->_wsdl->addElement(array(
                        'name' => $outputElementName,
                        'type' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $complexTypeForElementName
                    ));
                    $outputParameters = $methodData['interface']['out']['parameters'];
                    $documentation = sprintf('Response container for the %s call.', $operationName);
                    $callInfo = array();
                    $callInfo['returned']['always']['calls'] = array($operationName);
                    $this->_processComplexType($complexTypeForElementName, $outputParameters, $documentation,
                        $callInfo);
                    $this->_wsdl->addMessage($outputMessageName, array(
                        'messageParameters' => array(
                            'element' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $outputElementName
                        )
                    ));
                }

                $this->_wsdl->addPortOperation($portType, $operationName, $inputMessageName, $outputMessageName);
                $bindingOperation = $this->_wsdl->addBindingOperation($binding, $operationName, $bindingInput, $bindingOutput);
                $this->_wsdl->addSoapOperation($bindingOperation, $operationName);
                // @TODO: implement faults binding
            }
        }

        return $this->_wsdl->toXML();
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
     * Process complex type and add it to WSDL.
     *
     * @param string $name
     * @param array $parameters
     * @param string $documentation
     * @param array $callInfo
     */
    protected function _processComplexType($name, array $parameters, $documentation = null, $callInfo = array())
    {
        $complexTypeParameters = array();
        foreach ($parameters as $parameterName => $parameterData) {
            $wsdlData = array();
            $parameterType = $parameterData['type'];
            $isRequired = isset($parameterData['required']) && $parameterData['required'];
            $annotationCallInfo = $callInfo;
            if (!$isRequired && isset($annotationCallInfo['requiredInput']['yes']['calls'])) {
                $annotationCallInfo['requiredInput']['no']['calls'] = $callInfo['requiredInput']['yes']['calls'];
                unset($annotationCallInfo['requiredInput']['yes']);
            }
            $defaultValue = isset($parameterData['default']) ? $parameterData['default'] : null;
            $wsdlData['annotation'] = $this->_getAnnotation($parameterData['documentation'], $parameterType,
                $annotationCallInfo, $defaultValue);

            if ($this->_resourceConfig->isArrayType($parameterType)) {
                $this->_processComplexTypeArray($parameterType, $callInfo);
                $typeNs = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
                $wsdlParameterType = $this->_resourceConfig->translateArrayTypeName($parameterType);
            } else {
                $wsdlData['minOccurs'] = $isRequired ? 1 : 0;
                $wsdlData['maxOccurs'] = 1;
                $typeNs = $this->_processComplexTypeParameter($parameterType, $callInfo);
                $wsdlParameterType = $parameterType;
            }

            $wsdlData['type'] = $typeNs . ':' . $wsdlParameterType;
            $complexTypeParameters[$parameterName] = $wsdlData;
        }

        $annotation = $this->_getAnnotation($documentation, $name);
        $this->_wsdl->addComplexTypeWithParameters($name, $complexTypeParameters, $annotation);
    }

    /**
     * Process complex type array.
     *
     * @param string $type
     * @param array $callInfo
     */
    protected function _processComplexTypeArray($type, $callInfo)
    {
        $arrayItemType = $this->_resourceConfig->getArrayItemType($type);
        $arrayTypeName = $this->_resourceConfig->translateArrayTypeName($type);
        $arrayItemDocumentation = sprintf('An item of %s.', $arrayTypeName);
        $arrayItemAnnotation = $this->_getAnnotation($arrayItemDocumentation, $arrayItemType, $callInfo);
        $typeNs = $this->_processComplexTypeParameter($arrayItemType, $callInfo);
        $arrayTypeParameters = array(
            self::ARRAY_ITEM_KEY_NAME => array(
                'type' => $typeNs . ':' . $arrayItemType,
                'minOccurs' => 0,
                'maxOccurs' => 'unbounded',
                'annotation' => $arrayItemAnnotation
            )
        );
        $documentation = sprintf('An array of %s complex type items.', $arrayItemType);
        $annotation = $this->_getAnnotation($documentation, $arrayItemType);
        $this->_wsdl->addComplexTypeWithParameters($arrayTypeName, $arrayTypeParameters, $annotation);
    }

    /**
     * Process complex type parameter type and return it's namespace.
     * If parameter type is a complex type and has not been processed yet - recursively process it.
     *
     * @param string $type
     * @param array $parentCallInfo
     * @return string - xsd or tns
     */
    protected function _processComplexTypeParameter($type, $parentCallInfo = array())
    {
        if (!$this->_resourceConfig->isTypeSimple($type) && !in_array($type, $this->_processedTypes)) {
            $this->_processedTypes[] = $type;
            $data = $this->_resourceConfig->getDataType($type);
            $parameters = isset($data['parameters']) ? $data['parameters'] : array();
            $documentation = isset($data['documentation']) ? $data['documentation'] : null;
            $callInfo = array_replace_recursive($parentCallInfo, $this->_getComplexTypeCallInfo($type));
            $this->_processComplexType($type, $parameters, $documentation, $callInfo);
        }

        return $this->_resourceConfig->isTypeSimple($type)
            ? Mage_Webapi_Model_Soap_Wsdl::XSD_NS
            : Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
    }

    /**
     * Find in which operations given type used.
     *
     * @param string $type
     * @return array
     */
    protected function _getComplexTypeCallInfo($type)
    {
        return isset($this->_typeCallInfo[$type]) ?  $this->_typeCallInfo[$type] : array();
    }

    /**
     * Collect data about complex types call info.
     * Walks through all requested resources and checks all methods 'in' and 'out' parameters.
     *
     * @param array $requestedResources
     */
    protected function _collectCallInfo($requestedResources)
    {
        if (is_null($this->_typeCallInfo)) {
            foreach ($requestedResources as $resourceName => $resourceData) {
                foreach ($resourceData['methods'] as $methodName => $methodData) {
                    foreach ($methodData['interface'] as $direction => $interface) {
                        $direction = ($direction == 'in') ? 'requiredInput' : 'returned';
                        foreach ($interface['parameters'] as $parameterData) {
                            $parameterType = $parameterData['type'];
                            if (!$this->_resourceConfig->isTypeSimple($parameterType)) {
                                $operation = $this->getOperationName($resourceName, $methodName);
                                if ($parameterData['required']) {
                                    $condition = ($direction == 'requiredInput') ? 'yes' : 'always';
                                } else {
                                    $condition = $direction == 'requiredInput' ? 'no' : 'conditionally';
                                }
                                $this->_typeCallInfo[$parameterType][$direction][$condition]['calls'][] = $operation;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Generate annotation data for WSDL.
     * Convert all {key:value} from documentation into appinfo nodes.
     * Override default callInfo values if defined in parameter documentation.
     *
     * @param string $documentation parameter documentation string
     * @param string $parameterType
     * @param array $callInfo callInfo list for given parameter
     * @param null $default
     * @return array
     */
    protected function _getAnnotation($documentation, $parameterType, $callInfo = array(), $default = null)
    {
        $appInfo = array();
        if ($parameterType == 'boolean') {
            $default = (bool)$default ? 'true' : 'false';
        }
        if ($default) {
            $appInfo['default'] = $default;
        }
        if ($this->_resourceConfig->isArrayType($parameterType)) {
            $appInfo['natureOfType'] = 'array';
        }
        if (preg_match_all('/\{(.*)\:(.*)\}/U', $documentation, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $appinfoTag = $matches[0][$i];
                $tagName = $matches[1][$i];
                $tagValue  = $matches[2][$i];
                switch ($tagName) {
                    case 'callInfo':
                        $callInfoRegExp = '/([a-z].*)\:(returned|requiredInput)\:(yes|no|always|conditionally)/i';
                        if (preg_match($callInfoRegExp, $tagValue)) {
                            list($callName, $direction, $condition) = explode(':', $tagValue);
                            if (preg_match('/allCallsExcept\(([a-z].*)\)/', $callName, $calls)) {
                                $callInfo[$direction][$condition] = array(
                                    'allCallsExcept' => $calls[1],
                                );
                            } else if (!isset($callInfo[$direction][$condition]['allCallsExcept'])) {
                                $this->_overrideCallInfoName($callInfo, $callName);
                                $callInfo[$direction][$condition]['calls'][] = $callName;
                            }
                        }
                        break;
                    case 'seeLink':{
                        if (preg_match('/([http\:\/\/]?.*)\:(.*):(.*)/', $tagValue, $linkMatches)) {
                            $appInfo['seeLink'] = array(
                                'url' => $linkMatches[1],
                                'title' => $linkMatches[2],
                                'for' => $linkMatches[3],
                            );
                        }
                        break;
                    }
                    case 'docInstructions':
                        if (preg_match('/(input|output)\:(.*)/', $tagValue, $docMatches)) {
                            $appInfo['docInstructions'][$docMatches[1]] = $docMatches[2];
                        }
                        break;
                    default:
                        $appInfo[$tagName] = $tagValue;
                        break;
                }
                $documentation = str_replace($appinfoTag, '', $documentation);
            }
        }
        $appInfo['callInfo'] = $callInfo;

        return array(
            'documentation' => $documentation,
            'appinfo' => $appInfo,
        );
    }

    /**
     * Delete callName if it's already defined in some direction group.
     *
     * @param $callInfo
     * @param $callName
     */
    protected function _overrideCallInfoName(&$callInfo, $callName)
    {
        foreach ($callInfo as &$callInfoData) {
            foreach ($callInfoData as &$data) {
                if (isset($data['calls'])) {
                    $foundCallNameIndex = array_search($callName, $data['calls']);
                    if ($foundCallNameIndex !== false) {
                        unset($data['calls'][$foundCallNameIndex]);
                        break;
                    }
                }
            }
        }
    }
}
