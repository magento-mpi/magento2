<?php
/**
 * WSDL Generation class.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Wsdl
{
    /**#@+
     * XML Namespaces.
     */
    const XML_NS = 'xmlns';
    const XML_NS_URI = 'http://www.w3.org/2000/xmlns/';
    const WSDL_NS = 'wsdl';
    const WSDL_NS_URI = 'http://schemas.xmlsoap.org/wsdl/';
    const SOAP_NS = 'soap12';
    const SOAP_NS_URI = 'http://schemas.xmlsoap.org/wsdl/soap12/';
    const XSD_NS = 'xsd';
    const XSD_NS_URI = 'http://www.w3.org/2001/XMLSchema';
    const TYPES_NS = 'tns';
    /**#@-*/

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
     * Types defined on schema
     *
     * @var array
     */
    protected $_includedTypes = array();

    /**
     * @var string
     * */
    protected $_uri;

    /**
     * Set resource config property and required namespaces.
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        if (!isset($options['name'])) {
            throw new InvalidArgumentException('"name" option is required.');
        }
        if (!isset($options['uri'])) {
            throw new InvalidArgumentException('"uri" option is required.');
        }
        $this->_uri = $options['uri'];

        $this->_dom = new DOMDocument('1.0', 'utf-8');
        $definitions = $this->_dom->createElement(self::WSDL_NS . ':definitions');

        $definitions->setAttributeNS(self::XML_NS_URI, self::XML_NS . ':' . self::WSDL_NS, self::WSDL_NS_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, self::XML_NS . ':' . self::SOAP_NS, self::SOAP_NS_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, self::XML_NS . ':' . self::XSD_NS, self::XSD_NS_URI);
        $targetNamespace = $this->getTargetNamespace();
        $definitions->setAttributeNS(self::XML_NS_URI, self::XML_NS . ':' . self::TYPES_NS, $targetNamespace);
        $definitions->setAttribute('targetNamespace', $targetNamespace);
        $definitions->setAttribute('name', $options['name']);
        $this->_dom->appendChild($definitions);
        $this->_wsdl = $this->_dom->documentElement;
        $this->addSchemaTypeSection();
    }

    /**
     * Retrieve target namespace of the WSDL document.
     *
     * @return string
     */
    public function getTargetNamespace()
    {
        // TODO: investigate URL target namespace in Java.
        return sprintf('urn:Magento-%s', md5($this->_uri));
    }

    /**
     * This function makes sure a complex types section and schema additions are set.
     *
     * @return Mage_Webapi_Model_Soap_Wsdl
     */
    public function addSchemaTypeSection()
    {
        if ($this->_schema === null) {
            $this->_schema = $this->_dom->createElement(self::XSD_NS . ':schema');
            $this->_schema->setAttribute('targetNamespace', $this->getTargetNamespace());
            $types = $this->_dom->createElement(self::WSDL_NS . ':types');
            $types->appendChild($this->_schema);
            $this->_wsdl->appendChild($types);
        }
        return $this;
    }

    /**
     * Return the Schema node of the WSDL
     *
     * @return DOMElement
     */
    public function getSchema()
    {
        if ($this->_schema == null) {
            $this->addSchemaTypeSection();
        }

        return $this->_schema;
    }

    /**
     * Add element to types.
     *
     * @param array $data
     * @return DOMElement
     */
    public function addElement($data)
    {
        $schema = $this->getSchema();
        $element = $this->_dom->createElement(self::XSD_NS . ':element');
        foreach ($data as $attributeName => $attributeValue) {
            $element->setAttribute($attributeName, $attributeValue);
        }

        $schema->appendChild($element);

        return $element;
    }

    /**
     * Add complex type with predefined list of parameters.
     *
     * @param string $name
     * @param array $parameters
     * @param array $annotation
     * @return mixed
     */
    public function addComplexTypeWithParameters($name, array $parameters, $annotation = null)
    {
        if (!isset($this->_includedTypes[$name])) {
            $complexType = $this->_dom->createElement(self::XSD_NS . ':complexType');
            $complexType->setAttribute('name', $name);

            if (!empty($annotation)) {
                $this->addAnnotation($complexType, $annotation);
            }
            if (!empty($parameters)) {
                $sequence = $this->_dom->createElement(self::XSD_NS . ':sequence');
                foreach ($parameters as $parameterName => $parameterData) {
                    $element = $this->_dom->createElement(self::XSD_NS . ':element');
                    $element->setAttribute('name', $parameterName);
                    $element->setAttribute('type', $parameterData['type']);
                    if (isset($parameterData['minOccurs'])) {
                        $element->setAttribute('minOccurs', $parameterData['minOccurs']);
                    }
                    if (isset($parameterData['maxOccurs'])) {
                        $element->setAttribute('maxOccurs', $parameterData['maxOccurs']);
                    }
                    if (isset($parameterData['annotation'])) {
                        $this->addAnnotation($element, $parameterData['annotation']);
                    }
                    $sequence->appendChild($element);
                }
                $complexType->appendChild($sequence);
            }
            $this->getSchema()->appendChild($complexType);
            $this->_includedTypes[$name] = $complexType;
        }

        return $this->_includedTypes[$name];
    }

    /**
     * Add annotation node to given node.
     *
     * @param DOMElement $parentNode
     * @param array $data
     * @return DOMElement
     */
    public function addAnnotation(DOMElement $parentNode, $data)
    {
        $annotationNode = $this->_dom->createElement(self::XSD_NS . ':annotation');
        $documentationNode = $this->_dom->createElement(self::XSD_NS . ':documentation');
        $documentationText = isset($data['documentation']) ? trim($data['documentation']) : '';
        $documentationNode->appendChild($this->_dom->createTextNode($documentationText));
        $annotationNode->appendChild($documentationNode);

        $appInfoNode = $this->_dom->createElement(self::XSD_NS . ':appinfo');
        if (isset($data['appinfo'])) {
            foreach ($data['appinfo'] as $appInfoKey => $appInfoData) {
                switch ($appInfoKey) {
                    case 'callInfo':
                        foreach ($appInfoData as $direction => $conditions) {
                            foreach ($conditions as $condition => $info) {
                                $callInfoNode = $this->_dom->createElement('callInfo');
                                if (isset($info['allCallsExcept'])) {
                                    $allExceptNode = $this->_dom->createElement('allCallsExcept');
                                    $allExceptNode->appendChild($this->_dom->createTextNode($info['allCallsExcept']));
                                    $callInfoNode->appendChild($allExceptNode);
                                } else if (isset($info['calls'])) {
                                    foreach ($info['calls'] as $callName) {
                                        $callNode = $this->_dom->createElement('callName');
                                        $callNode->appendChild($this->_dom->createTextNode($callName));
                                        $callInfoNode->appendChild($callNode);
                                    }
                                }
                                $directionNode = $this->_dom->createElement($direction);
                                $directionNode->appendChild($this->_dom->createTextNode(ucfirst($condition)));
                                $callInfoNode->appendChild($directionNode);
                                $appInfoNode->appendChild($callInfoNode);
                            }
                        }
                        break;
                    case 'seeLink':
                        $seeLinkNode = $this->_dom->createElement('seeLink');
                        foreach (array('url', 'title', 'for') as $subNodeName) {
                            if (isset($appInfoData[$subNodeName])) {
                                $seeLinkSubNode = $this->_dom->createElement($subNodeName);
                                $seeLinkSubNode->appendChild($this->_dom->createTextNode($appInfoData[$subNodeName]));
                                $seeLinkNode->appendChild($seeLinkSubNode);
                            }
                        }
                        $appInfoNode->appendChild($seeLinkNode);
                        break;
                    default:
                        $appInfoData = trim($appInfoData);
                        $annotationTextNode = $this->_dom->createElement($appInfoKey);
                        $annotationTextNode->appendChild($this->_dom->createTextNode($appInfoData));
                        $appInfoNode->appendChild($annotationTextNode);
                        break;
                }
            }
        }
        $annotationNode->appendChild($appInfoNode);
        $parentNode->appendChild($annotationNode);

        return $annotationNode;
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
     * @param array|bool $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return DOMElement The new Operation's DOMElement for use with {@link function _addSoapOperation}
     */
    public function addBindingOperation(DOMElement $binding, $name, $input = false, $output = false, $fault = false)
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

    /**
     * Return the WSDL as XML
     *
     * @return string WSDL as XML
     */
    public function toXML()
    {
        return $this->_dom->saveXML();
    }
}
