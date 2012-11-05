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
            $strategy = new Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased($this->_resourceConfig);
            $this->_wsdl = new Mage_Webapi_Model_Soap_Wsdl(self::WSDL_NAME, $options['endpoint_url'], $strategy);
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
        $this->_wsdl->addSchemaTypeSection();

        foreach ($requestedResources as $resourceName => $resourceData) {
            $portTypeName = $this->getPortTypeName($resourceName);
            $bindingName = $this->getBindingName($resourceName);
            $portType = $this->_wsdl->addPortType($portTypeName);
            $binding = $this->_wsdl->addBinding($bindingName, $portTypeName);
            $this->_wsdl->addSoapBinding($binding, 'document', 'http://schemas.xmlsoap.org/soap/http', SOAP_1_2);
            $portName = $this->getPortName($resourceName);
            $serviceName = $this->getServiceName($resourceName);
            $this->_wsdl->addService($serviceName, $portName, 'tns:'.$bindingName, $this->_endpointUrl, SOAP_1_2);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $this->getOperationName($resourceName, $methodName);
                $inputBinding = array('use' => 'literal');
                $inputMessageName = $this->_createOperationInput($operationName, $methodData);

                $outputMessageName = false;
                $outputBinding = false;
                if (isset($methodData['interface']['out']['parameters'])) {
                    $outputBinding = $inputBinding;
                    $outputMessageName = $this->_createOperationOutput($operationName, $methodData);
                }

                $this->_wsdl->addPortOperation($portType, $operationName, $inputMessageName, $outputMessageName);
                $bindingOperation = $this->_wsdl->addBindingOperation($binding, $operationName, $inputBinding,
                    $outputBinding, false, SOAP_1_2);
                $this->_wsdl->addSoapOperation($bindingOperation, $operationName, SOAP_1_2);
                // @TODO: implement faults binding
            }
        }

        return $this->_wsdl->toXML();
    }

    /**
     * Create input message and corresponding element and complex types in WSDL.
     *
     * @param string $operationName
     * @param array $methodData
     * @return string input message name
     */
    protected function _createOperationInput($operationName, $methodData)
    {
        $inputMessageName = $this->getInputMessageName($operationName);
        $complexTypeName = $this->getElementComplexTypeName($inputMessageName);
        $inputParameters = array();
        $elementData = array(
            'name' => $inputMessageName,
            'type' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $complexTypeName
        );
        if (isset($methodData['interface']['in']['parameters'])) {
            $inputParameters = $methodData['interface']['in']['parameters'];
        } else {
            $elementData['nillable'] = 'true';
        }
        $this->_wsdl->addElement($elementData);
        $callInfo = array();
        $callInfo['requiredInput']['yes']['calls'] = array($operationName);
        $typeData = array(
            'documentation' => $methodData['documentation'],
            'parameters' => $inputParameters,
            'callInfo' => $callInfo,
        );
        $this->_resourceConfig->setTypeData($complexTypeName, $typeData);
        $this->_wsdl->addComplexType($complexTypeName);
        $this->_wsdl->addMessage($inputMessageName, array(
            'messageParameters' => array(
                'element' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $inputMessageName
            )
        ));

        return Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $inputMessageName;
    }

    /**
     * Create output message and corresponding element and complex types in WSDL.
     *
     * @param $operationName
     * @param $methodData
     * @return string output message name
     */
    protected function _createOperationOutput($operationName, $methodData)
    {
        $outputMessageName = $this->getOutputMessageName($operationName);
        $complexTypeName = $this->getElementComplexTypeName($outputMessageName);
        $this->_wsdl->addElement(array(
            'name' => $outputMessageName,
            'type' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $complexTypeName
        ));
        $callInfo = array();
        $callInfo['returned']['always']['calls'] = array($operationName);
        $typeData = array(
            'documentation' => sprintf('Response container for the %s call.', $operationName),
            'parameters' => $methodData['interface']['out']['parameters'],
            'callInfo' => $callInfo,
        );
        $this->_resourceConfig->setTypeData($complexTypeName, $typeData);
        $this->_wsdl->addComplexType($complexTypeName);
        $this->_wsdl->addMessage($outputMessageName, array(
            'messageParameters' => array(
                'element' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $outputMessageName
            )
        ));

        return Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $outputMessageName;
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
     * Get name for resource service
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
                            $callInfo = array();
                            $callInfo[$direction][$condition]['calls'][] = $operation;
                            $this->_resourceConfig->setTypeData($parameterType, array('callInfo' => $callInfo));
                        }
                    }
                }
            }
        }
    }

}
