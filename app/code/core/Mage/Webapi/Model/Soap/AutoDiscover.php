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
     * List of requested resources.
     *
     * @var array
     */
    protected $_requestedResources;

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

        if (!isset($options['requested_resources'])) {
            throw new InvalidArgumentException('"requested_resources" option is required.');
        }
        $this->_requestedResources = $options['requested_resources'];

        if (!isset($options['endpoint_url'])) {
            throw new InvalidArgumentException('"endpoint_url" option is required.');
        }
        $this->_endpointUrl = $options['endpoint_url'];

        if (isset($options['wsdl']) && $options['wsdl'] instanceof Mage_Webapi_Model_Soap_Wsdl) {
            $this->_wsdl = $options['wsdl'];
        } else {
            $this->_wsdl = Mage::getModel('Mage_Webapi_Model_Soap_Wsdl', array(
                'name' => self::WSDL_NAME,
                'uri' => $options['endpoint_url'],
            ));
        }
    }

    /**
     * Generate WSDL file based on requested resources.
     *
     * @return string
     */
    public function generate()
    {
        $service = $this->_wsdl->addService(self::SERVICE_NAME);

        foreach ($this->_requestedResources as $resourceName => $resourceData) {
            $portTypeName = $resourceName . 'PortType';
            $bindingName = $resourceName . 'Binding';
            $portType = $this->_wsdl->addPortType($portTypeName);
            $binding = $this->_wsdl->addBinding($bindingName, $portTypeName);
            $this->_wsdl->addSoapBinding($binding);
            $this->_wsdl->addServicePort($service, $resourceName . 'Port', $bindingName, $this->_endpointUrl);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $resourceName . ucfirst($methodName);

                $bindingInput = array('use' => 'literal');
                $inputMessageName = $operationName . 'Request';
                $inputTypeName = $operationName . 'Request';
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
                $this->_processComplexType($complexTypeForElementName, $inputParameters, $methodData['documentation']);
                $this->_wsdl->addMessage($inputMessageName, array(
                    'messageParameters' => array(
                        'element' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $inputTypeName
                    )
                ));

                $outputMessageName = null;
                $bindingOutput = null;
                if (isset($methodData['interface']['out']['parameters'])) {
                    $bindingOutput = array('use' => 'literal');
                    $outputMessageName = $operationName . 'Response';
                    $outputElementName = $operationName . 'Response';
                    $complexTypeForElementName = ucfirst($outputElementName);
                    $this->_wsdl->addElement(array(
                        'name' => $outputElementName,
                        'type' => Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $complexTypeForElementName
                    ));
                    $outputParameters = $methodData['interface']['out']['parameters'];
                    $this->_processComplexType($complexTypeForElementName, $outputParameters);
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
     * Process complex type and add it to WSDL.
     *
     * @param string $name
     * @param array $parameters
     * @param string $documentation
     */
    protected function _processComplexType($name, array $parameters, $documentation = null)
    {
        $complexTypeParameters = array();
        foreach ($parameters as $parameterName => $parameterData) {
            $wsdlData = array('documentation' => $parameterData['documentation']);
            $parameterType = $parameterData['type'];
            if ($this->_resourceConfig->isArrayType($parameterType)) {
                $this->_processComplexTypeArray($parameterType);
                $typeNs = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
                $parameterType = $this->_resourceConfig->translateArrayTypeName($parameterType);
            } else {
                $wsdlData['minOccurs'] = (isset($parameterData['required']) && $parameterData['required']) ? 1 : 0;
                $wsdlData['maxOccurs'] = 1;
                $typeNs = $this->_processComplexTypeParameter($parameterType);
            }
            $wsdlData['type'] = $typeNs . ':' . $parameterType;
            $complexTypeParameters[$parameterName] = $wsdlData;
        }

        $this->_wsdl->addComplexTypeWithParameters($name, $complexTypeParameters, $documentation);
    }

    /**
     * Process complex type array.
     *
     * @param string $type
     */
    protected function _processComplexTypeArray($type)
    {
        $arrayItemType = $this->_resourceConfig->getArrayItemType($type);
        $typeNs = $this->_processComplexTypeParameter($arrayItemType);
        $arrayTypeParameters = array(
            self::ARRAY_ITEM_KEY_NAME => array(
                'type' => $typeNs . ':' . $arrayItemType,
                'minOccurs' => 0,
                'maxOccurs' => 'unbounded'
            )
        );
        $arrayTypeName = $this->_resourceConfig->translateArrayTypeName($type);
        $this->_wsdl->addComplexTypeWithParameters($arrayTypeName, $arrayTypeParameters);
    }

    /**
     * Process complex type parameter type and return it's namespace.
     * If parameter type is a complex type and has not been processed yet - recursively process it.
     *
     * @param string $type
     * @return string - xsd or tns
     */
    protected function _processComplexTypeParameter($type)
    {
        if (!$this->_resourceConfig->isTypeSimple($type) && !in_array($type, $this->_processedTypes)) {
            $this->_processedTypes[] = $type;
            $data = $this->_resourceConfig->getDataType($type);
            $parameters = isset($data['parameters']) ? $data['parameters'] : array();
            $documentation = isset($data['documentation']) ? $data['documentation'] : null;
            $this->_processComplexType($type, $parameters, $documentation);
        }

        return $this->_resourceConfig->isTypeSimple($type)
            ? Mage_Webapi_Model_Soap_Wsdl::XSD_NS
            : Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
    }
}
