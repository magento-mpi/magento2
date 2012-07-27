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
 * API Resource Config
 *
 * @category Mage
 * @package  Mage_Api2
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Config_Resource extends Magento_Config_XmlAbstract
{
    /**
     * List of all defined custom data types
     *
     * @var array
     */
    protected $_dataTypes = array();

    /**
     * List of all defined parameters sets
     *
     * @var array
     */
    protected $_params = array();

    /**
     * @var array
     */
    protected $_paramsElementNameByMessageName = array();

    /**
     * Retrieve method data for for given resource name and method name.
     *
     * @param $resourceName
     * @param $methodName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getResourceMethodData($resourceName, $methodName)
    {
        if (!array_key_exists($resourceName, $this->_data['resources'])) {
            throw new InvalidArgumentException(
                sprintf('Resource "%s" not found in config.', $resourceName));
        }
        if (!array_key_exists($methodName, $this->_data['resources'][$resourceName])) {
            throw new InvalidArgumentException(
                sprintf('Method "%s" for resource "%s" not found in config.', $methodName, $resourceName));
        }
        return $this->_data['resources'][$resourceName][$methodName];
    }

    /**
     * Retrieve list of resources with methods
     *
     * @return array
     */
    public function getResources()
    {
        return $this->_data['resources'];
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @throws Magento_Exception
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        $this->_paramsElementNameByMessageName = $this->_extractMessages($dom);
        $this->_dataTypes = $this->_extractDataTypes($dom);
        $this->_params = $this->_extractOperationsParams($dom);

        $result = array();
        $result['resources'] = array();
        $result['types'] = $this->_dataTypes;

        /** @var DOMElement $portType */
        foreach ($dom->getElementsByTagName('portType') as $portType) {
            $resourceName = $portType->getAttribute('name');
            /** @var DOMElement $operation */
            foreach ($portType->getElementsByTagName('operation') as $operation) {
                $operationName = $operation->getAttribute('name');
                if (strpos($operationName, $resourceName) !== 0) {
                    throw new Magento_Exception(
                        sprintf('Operation "%s" has no relation to resource "%s".', $operationName, $resourceName));
                }
                $methodName = lcfirst(substr($operationName, strlen($resourceName)));
                $result['resources'][$resourceName][$methodName] = $this->_getOperationData($operation);
            }
        }
        return $result;
    }

    /**
     * Get links between operations' input/output messages elements and its parameters elements
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractMessages(DOMDocument $dom)
    {
        $messages = array();
        /** @var DOMElement $message */
        foreach ($dom->getElementsByTagName('message') as $message) {
            // definitions/message/part
            $part = $message->getElementsByTagName('part')->item(0);
            $elementName = $this->_cleanFromNamespace($part->getAttribute('element'));
            $messages[$message->getAttribute('name')] = $elementName;
        }
        return $messages;
    }

    /**
     * Get data about all sets of parameters
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractOperationsParams(DOMDocument $dom)
    {
        $result = array();
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('/wsdl:definitions/wsdl:types/xs:schema/xs:element');
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            $result[$element->getAttribute('name')] = $this->_getParams($element);
        }
        return $result;
    }

    /**
     * Get all defined custom data types
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractDataTypes(DOMDocument $dom)
    {
        $result = array();
        $xpath = new DOMXPath($dom);
        $types = $xpath->query('/wsdl:definitions/wsdl:types/xs:schema/xs:complexType');
        /** @var DOMElement $type */
        foreach ($types as $type) {
            $result[$type->getAttribute('name')] = $this->_getParams($type);
        }
        return $result;
    }

    /**
     * Get full operation's description: description, input and output parameters
     *
     * @param DOMElement $operation
     * @return array
     */
    protected function _getOperationData(DOMElement $operation)
    {
        $result = array();
        $result['description'] = $operation->getElementsByTagName('documentation')->item(0)->nodeValue;
        $result['input'] = $this->_getOperationParams($operation->getElementsByTagName('input')->item(0));
        $result['output'] = $this->_getOperationParams($operation->getElementsByTagName('output')->item(0));

        return $result;
    }

    /**
     * Get operation's parameters for its given input or output element
     *
     * @param DOMElement $operationInputOutputElement
     * @return array
     * @throws Magento_Exception
     */
    protected function _getOperationParams(DOMElement $operationInputOutputElement)
    {
        // definitions/portType/operation/input[@message]
        // definitions/portType/operation/output[@message]
        $messageName = $this->_cleanFromNamespace($operationInputOutputElement->getAttribute('message'));
        if (!array_key_exists($messageName, $this->_paramsElementNameByMessageName)) {
            throw new Magento_Exception(
                sprintf('There is no proper element with parameters for message "%s".', $messageName));
        }

        $paramsElementName = $this->_paramsElementNameByMessageName[$messageName];
        if (!array_key_exists($paramsElementName, $this->_params)) {
            throw new Magento_Exception(
                sprintf('There is no element "%s" with parameters not found.', $paramsElementName));
        }
        return $this->_params[$paramsElementName];
    }

    /**
     * Get list of parameters for given element
     *
     * @param DOMElement $element
     * @return array
     */
    protected function _getParams(DOMElement $element)
    {
        $result = array();
        $params = $element->getElementsByTagName('element');
        foreach ($params as $param) {
            $result[$param->getAttribute('name')] = array(
                'type' => $this->_getType($param),
                'required' => $this->_getIsRequired($param),
                'maxOccurs' => $this->_getMaxOccurs($param),
            );
        }
        return $result;
    }

    /**
     * Is given parameter required?
     *
     * @param DOMElement $param
     * @return boolean
     */
    protected function _getIsRequired(DOMElement $param)
    {
        return (bool)$param->getAttribute('minOccurs');
    }

    /**
     * Get number of occurrence for given parameter in request or responce
     * Can be numeric string or "unbounded"
     *
     * @param DOMElement $param
     * @return string
     */
    protected function _getMaxOccurs(DOMElement $param)
    {
        $maxOccurs = $param->getAttribute('maxOccurs');
        if ($maxOccurs === '') {
            $maxOccurs = '1';
        }
        return $maxOccurs;
    }

    /**
     * Get type for given parameter.
     * Can be simple type (string, int, etc) or complex type defined in config
     *
     * @param DOMElement $param
     * @return string
     */
    protected function _getType(DOMElement $param)
    {
        return $this->_cleanFromNamespace($param->getAttribute('type'));
    }

    /**
     * Remove namespace prefix from given string
     *
     * @param string $string
     * @return string
     */
    protected function _cleanFromNamespace($string)
    {
        if (strpos($string, ':') !== false) {
            $array = explode(':', $string);
            $string = $array[1];
        }
        return $string;
    }

    /**
     * Get absolute path to validation.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/resource.xsd';
    }

    /**
     * Retrieve config DOM Document
     *
     * @return DOMDocument
     */
    public function getDom()
    {
        return $this->_domConfig->getDom();
    }

    /**
     * Get initial XML of a valid document
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><wsdl:definitions name="Magento"
              xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
              xmlns:tns="urn:Magento"
              xmlns:xs="http://www.w3.org/2001/XMLSchema"
              targetNamespace="urn:Magento"></wsdl:definitions>';
    }

    /**
     * Define id attributes for entities
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/wsdl:definitions/wsdl:portType' => 'name',
            '/wsdl:definitions/wsdl:portType/wsdl:operation' => 'name',
            '/wsdl:definitions/wsdl:message' => 'name',
            '/wsdl:definitions/wsdl:types/xs:schema/xs:complexType' => 'name',
            '/wsdl:definitions/wsdl:types/xs:schema/xs:element' => 'name',
        );
    }
}
