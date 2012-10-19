<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * SOAP WSDL Generator.
 * It takes API resource config and creates complete WSDL 1.1 file based on it.
 * API resource config describes abstract part of WSDL and this config is responsible for concrete part.
 */
class Mage_Webapi_Model_Config_Wsdl
{
    /**
     * WSDL name attribute value
     */
    const WSDL_NAME = 'Magento';
    const WSDL_NS = 'wsdl';
    const WSDL_NS_URI = 'http://schemas.xmlsoap.org/wsdl/';
    const SOAP_NS = 'soap12';
    const SOAP_NS_URI = 'http://schemas.xmlsoap.org/wsdl/soap12/';
    const XSD_NS = 'xsd';
    const XSD_NS_URI = 'http://www.w3.org/2001/XMLSchema';
    const TYPES_NS = 'tns';
    const SERVICE_NAME = 'Magento API';

    /**
     * @var Mage_Webapi_Model_Config_Resource
     * */
    protected $_resourceConfig;

    /**
     * @var array
     */
    protected $_requestedResources;

    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * @var DOMElement
     */
    protected $_wsdl;

    /**
     * @var DOMElement
     */
    protected $_schema;

    /**
     * @var array
     */
    protected $_complexTypes;

    /**
     * @var string
     * */
    protected $_endpointUrl;

    /**
     * WSDL namespace
     *
     * @var string
     */
    protected $_nsWsdl;

    /**
     * SOAP namespace
     *
     * @var string
     */
    protected $_nsSoap12;

    /**
     * Types namespace
     *
     * @var string
     */
    protected $_nsTypes;

    /**
     * Set resource config property and required namespaces.
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        if (!isset($options['resource_config'])) {
            throw new InvalidArgumentException('"resource_config" option is required.');
        }
        if (!isset($options['requested_resources'])) {
            throw new InvalidArgumentException('"requested_resources" option is required.');
        }
        if (!isset($options['endpoint_url'])) {
            throw new InvalidArgumentException('"endpoint_url" option is required.');
        }
        if (!$options['resource_config'] instanceof Mage_Webapi_Model_Config_Resource) {
            throw new InvalidArgumentException('Invalid resource config.');
        }
        $this->_resourceConfig = $options['resource_config'];
        $this->_requestedResources = $options['requested_resources'];
        $this->_endpointUrl = $options['endpoint_url'];

        $this->_dom = new DOMDocument('1.0', 'utf-8');
        $definitions = $this->_dom->createElement(self::WSDL_NS . ':definitions');
        $targetNamespace = $this->_endpointUrl;
        $definitions->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . self::WSDL_NS, self::WSDL_NS_URI);
        $definitions->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . self::SOAP_NS, self::SOAP_NS_URI);
        $definitions->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . self::XSD_NS, self::XSD_NS_URI);
        $definitions->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . self::TYPES_NS, $targetNamespace);
        $definitions->setAttribute('targetNamespace', $targetNamespace);
        $definitions->setAttribute('name', self::WSDL_NAME);
        $this->_dom->appendChild($definitions);
        $this->_wsdl = $this->_dom->documentElement;

        $this->_schema = $this->_dom->createElement(self::XSD_NS . ':schema');
        $this->_schema->setAttribute('targetNamespace', $targetNamespace);
        $types = $this->_dom->createElement(self::WSDL_NS . ':types');
        $types->appendChild($this->_schema);
        $this->_wsdl->appendChild($types);
    }

    /**
     * Generate WSDL file based on resource config.
     * It creates WSDL with single service called "MagentoAPI" and separate port for each resource.
     *
     * @return string
     */
    public function generate()
    {
        $service = $this->addService(self::SERVICE_NAME);

        foreach ($this->_requestedResources as $resourceName => $resourceData) {
            $portTypeName = $resourceName . 'PortType';
            $portType = $this->addPortType($portTypeName);
            $bindingName = $resourceName . 'Binding';
            $binding = $this->addBinding($bindingName, $portTypeName);
            $this->addSoapBinding($binding);
            $this->addServicePort($service, $resourceName . 'Port', $bindingName, $this->_endpointUrl);

            foreach ($resourceData['methods'] as $methodName => $methodData) {
                $operationName = $resourceName . ucfirst($methodName);
                $inputTypeName = $operationName . 'Request';
                if (isset($methodData['interface']['in'])) {
                    $this->addElement($inputTypeName, $methodData['interface']['in']['parameters']);
                }
                $inputMessageName = $operationName . 'Request';
                $this->addMessage($inputMessageName, array(
                    'messageParameters' => array(
                        'element' => self::TYPES_NS . ':' . $inputTypeName
                    )
                ));
                $outputTypeName = $operationName . 'Response';
                if (isset($methodData['interface']['out'])) {
                    $this->addElement($outputTypeName, $methodData['interface']['out']);
                }
                $outputMessageName = $operationName . 'Response';
                $this->addMessage($outputMessageName, array(
                    'messageParameters' => array(
                        'element' => self::TYPES_NS . ':' . $outputTypeName
                    )
                ));

                $this->addPortOperation($portType, $operationName, $inputMessageName, $outputMessageName);

                $input = $output = array('use' => 'literal');
                $soapOperation = $this->addBindingOperation($binding, $operationName, $input, $output);
                $this->addSoapOperation($soapOperation, $operationName);
                // @TODO: implement faults binding
            }
        }

        return $this->_dom->saveXML();
    }

    /**
     * Add element to types.
     *
     * @param string $name
     * @param array $parameters
     * @return DOMElement
     */
    public function addElement($name, $parameters)
    {
        $element = $this->_dom->createElement(self::XSD_NS . ':element');
        $complexTypeName = ucfirst($name);
        $element->setAttribute('name', $name);
        $element->setAttribute('type', self::TYPES_NS . ':' . $complexTypeName);
        $this->_schema->appendChild($element);
        $this->addComplexType($complexTypeName, $parameters);

        return $element;
    }

    /**
     * Add complex type.
     *
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function addComplexType($name, $parameters)
    {
        if (!isset($this->_complexTypes[$name])) {
            $complexType = $this->_dom->createElement(self::XSD_NS . ':complexType');
            $complexType->setAttribute('name', $name);

            $sequence = $this->_dom->createElement(self::XSD_NS . ':sequence');
            foreach ($parameters as $parameterName => $parameterData) {
                $element = $this->_dom->createElement(self::XSD_NS . ':element');
                $element->setAttribute('name', $parameterName);
                $typeNs = $this->_resourceConfig->isTypeSimple($parameterData['type']) ? self::XSD_NS : self::TYPES_NS;
                $element->setAttribute('type', $typeNs . ':' . $parameterData['type']);
                $isRequired = (isset($parameterData['required']) && $parameterData['required']) ? 1 : 0;
                $element->setAttribute('minOccurs', $isRequired);
                $element->setAttribute('maxOccurs', 1);
                $sequence->appendChild($element);
                if (!$this->_resourceConfig->isTypeSimple($parameterData['type'])) {
                    $this->addComplexType($parameterData['type'], $this->_resourceConfig->getDataType($parameterData['type']));
                }
            }
            $complexType->appendChild($sequence);
            $this->_schema->appendChild($complexType);
            $this->_complexTypes[$name] = $complexType;
        }

        return $this->_complexTypes[$name];
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_messages message} element to the WSDL
     *
     * @param string $name Name for the {@link http://www.w3.org/TR/wsdl#_messages message}
     * @param array $parts An array of {@link http://www.w3.org/TR/wsdl#_message parts}
     *                     The array is constructed like: 'name of part' => 'part xml schema data type'
     *                     or 'name of part' => array('type' => 'part xml schema type')
     *                     or 'name of part' => array('element' => 'part xml element name')
     * @return object The new message's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addMessage($name, $parts)
    {
        $message = $this->_dom->createElement(self::WSDL_NS . ':message');
        $message->setAttribute('name', $name);

        if (count($parts) > 0) {
            foreach ($parts as $name => $type) {
                $part = $this->_dom->createElement(self::WSDL_NS . ':part');
                $part->setAttribute('name', $name);
                if (is_array($type)) {
                    foreach ($type as $key => $value) {
                        $part->setAttribute($key, $value);
                    }
                } else {
                    $part->setAttribute('type', $type);
                }
                $message->appendChild($part);
            }
        }

        $this->_wsdl->appendChild($message);

        return $message;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_porttypes portType} element to the WSDL
     *
     * @param string $name portType element's name
     * @return DOMElement The new portType's XML_Tree_Node for use in {@link function addPortOperation} and {@link function addDocumentation}
     */
    public function addPortType($name)
    {
        $portType = $this->_dom->createElement(self::WSDL_NS . ':portType');
        $portType->setAttribute('name', $name);
        $this->_wsdl->appendChild($portType);

        return $portType;
    }

    /**
     * Add an {@link http://www.w3.org/TR/wsdl#request-response operation} element to a portType element
     *
     * @param DOMElement $portType a portType XML_Tree_Node, from {@link function addPortType}
     * @param string $name Operation name
     * @param bool $input Input Message
     * @param bool $output Output Message
     * @param bool $fault Fault Message
     * @return DOMElement The new operation's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addPortOperation(DOMElement $portType, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->_dom->createElement(self::WSDL_NS . ':operation');
        $operation->setAttribute('name', $name);

        if (is_string($input) && (strlen(trim($input)) >= 1)) {
            $node = $this->_dom->createElement(self::WSDL_NS . ':input');
            $node->setAttribute('message', self::TYPES_NS . ':' . $input);
            $operation->appendChild($node);
        }
        if (is_string($output) && (strlen(trim($output)) >= 1)) {
            $node= $this->_dom->createElement(self::WSDL_NS . ':output');
            $node->setAttribute('message', self::TYPES_NS . ':' . $output);
            $operation->appendChild($node);
        }
        if (is_string($fault) && (strlen(trim($fault)) >= 1)) {
            $node = $this->_dom->createElement(self::WSDL_NS . ':fault');
            $node->setAttribute('message', self::TYPES_NS . ':' . $fault);
            $operation->appendChild($node);
        }

        $portType->appendChild($operation);

        return $operation;
    }

    /**
     * Add a binding element to WSDL
     *
     * @param string $name Name of the Binding
     * @param string $portType name of the portType to bind
     * @return DOMElement
     */
    public function addBinding($name, $portType)
    {
        $binding = $this->_dom->createElement(self::WSDL_NS . ':binding');
        $binding->setAttribute('name', $name);
        $binding->setAttribute('type', self::TYPES_NS . ':' . $portType);

        $this->_wsdl->appendChild($binding);

        return $binding;
    }

    /**
     * Add an operation to a binding element
     *
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function _addBinding}
     * @param string $name
     * @param array|bool $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $inputHeader
     * @param array|bool $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return DOMElement The new Operation's DOMElement for use with {@link function _addSoapOperation}
     */
    public function addBindingOperation(DOMElement $binding, $name, $input = false, $output = false,
        $inputHeader = false, $fault = false)
    {
        $operation = $this->_dom->createElement(self::WSDL_NS . ':operation');
        $operation->setAttribute('name', $name);

        if (is_array($input)) {
            $node = $this->_dom->createElement(self::WSDL_NS . ':input');
            $soapNode = $this->_dom->createElement(self::SOAP_NS . ':body');
            foreach ($input as $name => $value) {
                $soapNode->setAttribute($name, $value);
            }
            $node->appendChild($soapNode);

            if (is_array($inputHeader)) {
                $headerNode = $this->_dom->createElement(self::SOAP_NS . ':header');
                foreach ($inputHeader as $name => $value) {
                    if ($name == 'message') {
                        $value = self::TYPES_NS . ':' . $value;
                    }
                    $headerNode->setAttribute($name, $value);
                }
                $node->appendChild($headerNode);
            }
            $operation->appendChild($node);
        }

        if (is_array($output)) {
            $node = $this->_dom->createElement(self::WSDL_NS . ':output');
            $soapNode = $this->_dom->createElement(self::SOAP_NS . ':body');
            foreach ($output as $name => $value) {
                $soapNode->setAttribute($name, $value);
            }
            $node->appendChild($soapNode);
            $operation->appendChild($node);
        }

        if (is_array($fault)) {
            $node = $this->_dom->createElement(self::WSDL_NS . ':fault');
            if (isset($fault['name'])) {
                $node->setAttribute('name', $fault['name']);
            }

            $soapNode = $this->_dom->createElement(self::SOAP_NS . ':fault');
            foreach ($fault as $name => $value) {
                $soapNode->setAttribute($name, $value);
            }
            $node->appendChild($soapNode);
            $operation->appendChild($node);
        }

        $binding->appendChild($operation);

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:binding SOAP binding} element to a Binding element
     *
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function _addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return DOMElement
     */
    public function addSoapBinding(DOMElement $binding, $style = 'document',
        $transport = 'http://schemas.xmlsoap.org/soap/http')
    {
        $soapBinding = $this->_dom->createElement(self::SOAP_NS . ':binding');
        $soapBinding->setAttribute('style', $style);
        $soapBinding->setAttribute('transport', $transport);

        $binding->appendChild($soapBinding);

        return $soapBinding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param DOMElement $operation An operation XML_Tree_Node returned by {@link function _addBindingOperation}
     * @param string $soapAction SOAP Action
     * @return DOMElement
     */
    public function addSoapOperation($operation, $soapAction)
    {
        $soapOperation = $this->_dom->createElement(self::SOAP_NS . ':operation');
        $soapOperation->setAttribute('soapAction', $soapAction);

        $operation->insertBefore($soapOperation, $operation->firstChild);

        return $soapOperation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @return DOMElement
     */
    public function addService($name)
    {
        $service = $this->_dom->createElement(self::WSDL_NS . ':service');
        $service->setAttribute('name', $name);

        $this->_wsdl->appendChild($service);
        return $service;
    }

    /**
     * Add port element to service.
     *
     * @param DOMElement $service
     * @param string $portName Name of the port for the service
     * @param string $binding Binding for the port
     * @param string $location SOAP Address for the service
     * @return DOMElement The new port XML_Tree_Node
     */
    public function addServicePort(DOMElement $service, $portName, $binding, $location)
    {
        $port = $this->_dom->createElement(self::WSDL_NS . ':port');
        $port->setAttribute('name', $portName);
        $port->setAttribute('binding', self::TYPES_NS . ':' . $binding);

        $soapAddress = $this->_dom->createElement(self::SOAP_NS . ':address');
        $soapAddress->setAttribute('location', $location);

        $port->appendChild($soapAddress);
        $service->appendChild($port);

        return $port;
    }
}
