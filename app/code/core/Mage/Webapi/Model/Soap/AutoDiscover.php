<?php
/**
 * Auto discovery class for WSDL generation.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_AutoDiscover
{
    /**
     * WSDL name and Service name attributes value
     */
    const WSDL_NAME = 'MagentoWSDL';
    const SERVICE_NAME = 'MagentoAPI';

    /**
     * @var Mage_Webapi_Model_Config_Resource
     * */
    protected $_resourceConfig;

    /**
     * @var array
     */
    protected $_requestedResources;

    /**
     * @var Mage_Webapi_Model_Soap_Wsdl
     */
    protected $_wsdl;

    /**
     * @var string
     * */
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
            $portType = $this->_wsdl->addPortType($portTypeName);
            $bindingName = $resourceName . 'Binding';
            $binding = $this->_wsdl->addBinding($bindingName, $portTypeName);
            $this->_wsdl->addSoapBinding($binding);
            $this->_wsdl->addServicePort($service, $resourceName . 'Port', $bindingName, $this->_endpointUrl);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $resourceName . ucfirst($methodName);

                $bindingInput = array('use' => 'literal');
                $inputMessageName = $operationName . 'Request';
                $inputTypeName = $operationName . 'Request';
                $complexTypeForElementName = $inputTypeName . 'Type';
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
                    $complexTypeForElementName = $outputElementName . 'Type';
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
            $isRequired = (isset($parameterData['required']) && $parameterData['required']) ? 1 : 0;
            $typeNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;
            $type = $parameterData['type'];
            if (!$this->_resourceConfig->isTypeSimple($type)) {
                $typeData = $this->_resourceConfig->getDataType($type);
                $this->_processComplexType($type, $typeData['parameters'], $typeData['documentation']);
                $typeNs = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
            }
            $complexTypeParameters[$parameterName] = array(
                'type' => $typeNs . ':' . $type,
                'required' => $isRequired,
                'documentation' => $parameterData['documentation']
            );
        }

        $this->_wsdl->addComplexTypeWithParameters($name, $complexTypeParameters, $documentation);
    }
}
