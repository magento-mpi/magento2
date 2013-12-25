<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

use DOMElement;
use Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy\ConfigBased as ComplexTypeStrategy;

/**
 * Magento-specific WSDL builder.
 */
class Wsdl extends \Zend\Soap\Wsdl
{
    /**
     * Constructor.
     * Save URI for targetNamespace generation.
     *
     * @param string $name
     * @param string|\Zend\Uri\Uri $uri
     * @param ComplexTypeStrategy $strategy
     */
    public function __construct(
        $name,
        $uri,
        ComplexTypeStrategy $strategy
    ) {
        parent::__construct($name, $uri, $strategy);
    }

    /**
     * Add an operation to port type.
     *
     * Multiple faults generation is allowed, while it is not allowed in parent.
     *
     * @param DOMElement $portType
     * @param string $name Operation name
     * @param string|bool $input Input Message
     * @param string|bool $output Output Message
     * @param array|bool $fault array of Fault messages in the format: array(array('message' => ..., 'name' => ...))
     * @return object The new operation's XML_Tree_Node
     */
    public function addPortOperation($portType, $name, $input = false, $output = false, $fault = false)
    {
        $operation = parent::addPortOperation($portType, $name, $input, $output, false);
        if (is_array($fault)) {
            foreach ($fault as $faultInfo) {
                $isMessageValid = isset($faultInfo['message']) && is_string($faultInfo['message'])
                    && strlen(trim($faultInfo['message']));
                $isNameValid = isset($faultInfo['name']) && is_string($faultInfo['name'])
                    && strlen(trim($faultInfo['name']));

                if ($isNameValid && $isMessageValid) {
                    $node = $this->toDomDocument()->createElement('fault');
                    $node->setAttribute('name', $faultInfo['name']);
                    $node->setAttribute('message', $faultInfo['message']);
                    $operation->appendChild($node);
                }
            }
        }
        return $operation;
    }

    /**
     * Add an operation to a binding element.
     *
     * Multiple faults binding is allowed, while it is not allowed in parent.
     *
     * @param DOMElement $binding
     * @param string $name Operation name
     * @param bool|array $input An array of attributes for the input element,
     *      allowed keys are: 'use', 'namespace', 'encodingStyle'.
     * @param bool|array $output An array of attributes for the output element,
     *      allowed keys are: 'use', 'namespace', 'encodingStyle'.
     * @param bool|array $fault An array of arrays which contain fault names: array(array('name' => ...))).
     * @param int $soapVersion SOAP version to be used in binding operation. 1.1 used by default.
     * @return DOMElement The new Operation's XML_Tree_Node
     */
    public function addBindingOperation(
        $binding,
        $name,
        $input = false,
        $output = false,
        $fault = false,
        $soapVersion = SOAP_1_1
    ) {
        $operation = parent::addBindingOperation($binding, $name, $input, $output, false, $soapVersion);
        if (is_array($fault)) {
            foreach ($fault as $faultInfo) {
                $isNameValid = isset($faultInfo['name']) && is_string($faultInfo['name'])
                    && strlen(trim($faultInfo['name']));

                if ($isNameValid) {
                    $faultInfo['use'] = 'literal';
                    $wsdlFault = $this->toDomDocument()->createElement('fault');
                    $wsdlFault->setAttribute('name', $faultInfo['name']);

                    $soapFault = $this->toDomDocument()->createElement('soap:fault');
                    $soapFault->setAttribute('name', $faultInfo['name']);
                    $soapFault->setAttribute('use', 'literal');

                    $wsdlFault->appendChild($soapFault);
                    $operation->appendChild($wsdlFault);
                }
            }
        }
        return $operation;
    }
}
