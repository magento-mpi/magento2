<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Soap
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SOAP WSDL Generator class.
 */
class Magento_Soap_Wsdl
{
    /** @var DOMDocument */
    protected $_dom;

    /** @var DOMElement */
    protected $_wsdl;

    /** @var string WSDL namespace*/
    protected $_nsWsdl;

    /** @var string SOAP namespace */
    protected $_nsSoap12;

    /** @var string Types namespace */
    protected $_nsTypes;

    public function __construct($xml, $namespaceWsdl = 'wsdl', $namespaceSoap12 = 'soap12', $namespaceTypes = 'tns')
    {
        $this->_dom = new DOMDocument();
        $this->_dom->loadXML($xml);

        $this->_wsdl = $this->_dom->documentElement;
        $this->_nsWsdl = $namespaceWsdl;
        $this->_nsSoap12 = $namespaceSoap12;
        $this->_nsTypes = $namespaceTypes;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_bindings binding} element to WSDL
     *
     * @param string $name Name of the Binding
     * @param string $portType name of the portType to bind
     * @return DOMElement The new binding's XML_Tree_Node for use with {@link function addBindingOperation} and {@link function addDocumentation}
     */
    public function addBinding($name, $portType)
    {
        $binding = $this->_dom->createElement($this->_nsWsdl . ':binding');
        $binding->setAttribute('name', $name);
        $binding->setAttribute('type', $this->_nsTypes . ':' . $portType);

        $this->_wsdl->appendChild($binding);

        return $binding;
    }

    /**
     * Add an operation to a binding element
     *
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param string $name
     * @param array|bool $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return DOMElement The new Operation's XML_Tree_Node for use with {@link function addSoapOperation} and {@link function addDocumentation}
     */
    public function addBindingOperation(DOMElement $binding, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->_dom->createElement($this->_nsWsdl . ':operation');
        $operation->setAttribute('name', $name);

        if (is_array($input)) {
            $node = $this->_dom->createElement($this->_nsWsdl . ':input');
            $soapNode = $this->_dom->createElement($this->_nsSoap12 . ':body');
            foreach ($input as $name => $value) {
                $soapNode->setAttribute($name, $value);
            }
            $node->appendChild($soapNode);
            $operation->appendChild($node);
        }

        if (is_array($output)) {
            $node = $this->_dom->createElement($this->_nsWsdl . ':output');
            $soapNode = $this->_dom->createElement($this->_nsSoap12 . ':body');
            foreach ($output as $name => $value) {
                $soapNode->setAttribute($name, $value);
            }
            $node->appendChild($soapNode);
            $operation->appendChild($node);
        }

        if (is_array($fault)) {
            $node = $this->_dom->createElement($this->_nsWsdl . ':fault');
            /**
             * Note. Do we really need name attribute to be also set at wsdl:fault node???
             * W3C standard doesn't mention it (http://www.w3.org/TR/wsdl#_soap:fault)
             * But some real world WSDLs use it, so it may be required for compatibility reasons.
             */
            if (isset($fault['name'])) {
                $node->setAttribute('name', $fault['name']);
            }

            $soapNode = $this->_dom->createElement($this->_nsSoap12 . ':fault');
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
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return DOMElement
     */
    public function addSoapBinding(DOMElement $binding, $style = 'document',
        $transport = 'http://schemas.xmlsoap.org/soap/http')
    {
        $soapBinding = $this->_dom->createElement($this->_nsSoap12 . ':binding');
        $soapBinding->setAttribute('style', $style);
        $soapBinding->setAttribute('transport', $transport);

        $binding->appendChild($soapBinding);

        return $soapBinding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param DOMElement $operation An operation XML_Tree_Node returned by {@link function addBindingOperation}
     * @param string $soapAction SOAP Action
     * @return boolean
     */
    public function addSoapOperation($operation, $soapAction)
    {
        if ($soapAction instanceof Zend_Uri_Http) {
            $soapAction = $soapAction->getUri();
        }
        $soapOperation = $this->_dom->createElement($this->_nsSoap12 . ':operation');
        $soapOperation->setAttribute('soapAction', $soapAction);

        $operation->insertBefore($soapOperation, $operation->firstChild);

        return $soapOperation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @return DOMElement The new service's XML_Tree_Node for use with {@link function addDocumentation}
     */
    public function addService($name)
    {

        $service = $this->_dom->createElement($this->_nsWsdl . ':service');
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
     * @return DOMElement The new port's XML_Tree_Node
     */
    public function addServicePort(DOMElement $service, $portName, $binding, $location)
    {
        if ($location instanceof Zend_Uri_Http) {
            $location = $location->getUri();
        }

        $port = $this->_dom->createElement($this->_nsWsdl . ':port');
        $port->setAttribute('name', $portName);
        $port->setAttribute('binding', $this->_nsTypes . ':' . $binding);

        $soapAddress = $this->_dom->createElement($this->_nsSoap12 . ':address');
        $soapAddress->setAttribute('location', $location);

        $port->appendChild($soapAddress);
        $service->appendChild($port);

        return $port;
    }

    /**
     * @return DOMDocument
     */
    public function getDom()
    {
        return $this->_dom;
    }
}
