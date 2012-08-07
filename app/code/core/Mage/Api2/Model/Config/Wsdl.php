<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * SOAP WSDL Generator.
 * It takes API resource config and creates complete WSDL 1.1 file based on it.
 * API resource config describes abstract part of WSDL and this config is responsible for concrete part.
 */
class Mage_Api2_Model_Config_Wsdl
{
    /** @var Mage_Api2_Model_Config_Resource */
    protected $_resourceConfig;

    /** @var DOMDocument */
    protected $_dom;

    /** @var DOMElement */
    protected $_wsdl;

    /** @var string */
    protected $_endpointUrl;

    /** @var string WSDL namespace*/
    protected $_nsWsdl;

    /** @var string SOAP namespace */
    protected $_nsSoap12;

    /** @var string Types namespace */
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
        if (!isset($options['endpoint_url'])) {
            throw new InvalidArgumentException('"endpoint_url" option is required.');
        }
        if (!$options['resource_config'] instanceof Mage_Api2_Model_Config_Resource) {
            throw new InvalidArgumentException('Invalid resource config.');
        }
        $this->_resourceConfig = $options['resource_config'];
        $this->_dom = $this->_resourceConfig->getDom();
        $this->_wsdl = $this->_dom->documentElement;
        $this->_endpointUrl = $options['endpoint_url'];
        $this->_nsWsdl = isset($options['namespace_wsdl']) ? $options['namespace_wsdl'] : 'wsdl';
        $this->_nsSoap12 = isset($options['namespace_soap12']) ? $options['namespace_soap12'] : 'soap12';
        $this->_nsTypes = isset($options['namespace_types']) ? $options['namespace_types'] : 'tns';
    }

    /**
     * Generate WSDL file based on resource config.
     * It creates WSDL with single service called "MagentoAPI" and separate port for each resource.
     *
     * @return string
     */
    public function generate()
    {
        $service = $this->_addService('MagentoAPI');

        foreach ($this->_resourceConfig->getResources() as $resourceName => $methods) {
            $bindingName = ucfirst($resourceName);
            $binding = $this->_addBinding($bindingName, $resourceName);
            $this->_addSoapBinding($binding);
            $this->_addServicePort($service, $bindingName . '_Soap12', $bindingName, $this->_endpointUrl);

            foreach ($methods as $methodName => $methodData) {
                $input = $output = array('use' => 'literal');
                $inputHeader = array('message' => 'AuthorizationHeader', 'part' => 'Authorization', 'use' => 'literal');
                $operation = $this->_addBindingOperation($binding, $resourceName . ucfirst($methodName), $input,
                    $inputHeader, $output);
                $this->_addSoapOperation($operation, $resourceName . ucfirst($methodName));
                // @TODO: implement faults binding
            }
        }

        return $this->_dom->saveXML();
    }

    /**
     * Add a binding element to WSDL
     *
     * @param string $name Name of the Binding
     * @param string $portType name of the portType to bind
     * @return DOMElement
     */
    protected function _addBinding($name, $portType)
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
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function _addBinding}
     * @param string $name
     * @param array|bool $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $inputHeader
     * @param array|bool $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array|bool $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return DOMElement The new Operation's DOMElement for use with {@link function _addSoapOperation}
     */
    protected function _addBindingOperation(DOMElement $binding, $name, $input = false, $inputHeader = false, $output = false, $fault = false)
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

            if (is_array($inputHeader)) {
                $headerNode = $this->_dom->createElement($this->_nsSoap12 . ':header');
                foreach ($inputHeader as $name => $value) {
                    if ($name == 'message') {
                        $value = $this->_nsTypes . ':' . $value;
                    }
                    $headerNode->setAttribute($name, $value);
                }
                $node->appendChild($headerNode);
            }
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
     * @param DOMElement $binding A binding XML_Tree_Node returned by {@link function _addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return DOMElement
     */
    protected function _addSoapBinding(DOMElement $binding, $style = 'document',
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
     * @param DOMElement $operation An operation XML_Tree_Node returned by {@link function _addBindingOperation}
     * @param string $soapAction SOAP Action
     * @return DOMElement
     */
    protected function _addSoapOperation($operation, $soapAction)
    {
        $soapOperation = $this->_dom->createElement($this->_nsSoap12 . ':operation');
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
    protected function _addService($name)
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
     * @return DOMElement The new port XML_Tree_Node
     */
    protected function _addServicePort(DOMElement $service, $portName, $binding, $location)
    {
        $port = $this->_dom->createElement($this->_nsWsdl . ':port');
        $port->setAttribute('name', $portName);
        $port->setAttribute('binding', $this->_nsTypes . ':' . $binding);

        $soapAddress = $this->_dom->createElement($this->_nsSoap12 . ':address');
        $soapAddress->setAttribute('location', $location);

        $port->appendChild($soapAddress);
        $service->appendChild($port);

        return $port;
    }
}
