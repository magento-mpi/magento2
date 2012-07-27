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
    protected $_nsSoap;

    public function __construct($xml, $namespaceWsdl = 'wsdl', $namespaceSoap = 'soap')
    {
        $this->_dom = new DOMDocument();
        $this->_dom->loadXML($xml);

        $this->_wsdl = $this->_dom->documentElement;
        $this->_nsWsdl = $namespaceWsdl;
        $this->_nsSoap = $namespaceSoap;
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
        $binding->setAttribute('type', $portType);

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
            $soap_node = $this->_dom->createElement($this->_nsSoap . ':body');
            foreach ($input as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        if (is_array($output)) {
            $node = $this->_dom->createElement($this->_nsWsdl . ':output');
            $soap_node = $this->_dom->createElement($this->_nsSoap . ':body');
            foreach ($output as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
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

            $soap_node = $this->_dom->createElement($this->_nsSoap . ':fault');
            foreach ($fault as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
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
        $soap_binding = $this->_dom->createElement($this->_nsSoap . ':binding');
        $soap_binding->setAttribute('style', $style);
        $soap_binding->setAttribute('transport', $transport);

        $binding->appendChild($soap_binding);

        return $soap_binding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param DOMElement $binding An operation XML_Tree_Node returned by {@link function addBindingOperation}
     * @param string $soap_action SOAP Action
     * @return boolean
     */
    public function addSoapOperation($binding, $soap_action)
    {
        if ($soap_action instanceof Zend_Uri_Http) {
            $soap_action = $soap_action->getUri();
        }
        $soap_operation = $this->_dom->createElement($this->_nsSoap . ':operation');
        $soap_operation->setAttribute('soapAction', $soap_action);

        $binding->insertBefore($soap_operation, $binding->firstChild);

        return $soap_operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @param string $portName Name of the port for the service
     * @param string $binding Binding for the port
     * @param string $location SOAP Address for the service
     * @return object The new service's XML_Tree_Node for use with {@link function addDocumentation}
     */
    public function addService($name, $portName, $binding, $location)
    {
        if ($location instanceof Zend_Uri_Http) {
            $location = $location->getUri();
        }
        $service = $this->_dom->createElement($this->_nsWsdl . 'service');
        $service->setAttribute('name', $name);

        $port = $this->_dom->createElement($this->_nsWsdl . 'port');
        $port->setAttribute('name', $portName);
        $port->setAttribute('binding', $binding);

        $soap_address = $this->_dom->createElement($this->_nsSoap . ':address');
        $soap_address->setAttribute('location', $location);

        $port->appendChild($soap_address);
        $service->appendChild($port);

        $this->_wsdl->appendChild($service);

        return $service;
    }

    /**
     * @return DOMDocument
     */
    public function toDom()
    {
        return $this->_dom;
    }
}
