<?php
use Zend\Soap\Wsdl;

/**
 * Magento-specific WSDL builder.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_Wsdl extends Wsdl
{
    /**
     * @var string|Zend\Uri\Uri
     */
    protected $_uri;

    /**
     * Constructor.
     * Save URI for targetNamespace generation.
     *
     * @param string $name
     * @param string|Zend\Uri\Uri $uri
     * @param Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_AnyComplexType $strategy
     */
    public function __construct($name, $uri, Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_AnyComplexType $strategy)
    {
        $this->_uri = $uri;
        parent::__construct($name, $uri, $strategy);
    }

    /**
     * Add complex type definition
     *
     * @param DOMNode $complexTypeNode XSD of service method for input/output
     * @return string|null
     */
    public function addComplexType($complexTypeNode)
    {
        $this->addSchemaTypeSection();

        $strategy = $this->getComplexTypeStrategy();
        $strategy->setContext($this);
        // delegates the detection of a complex type to the current strategy
        return $strategy->addComplexType($complexTypeNode);
    }
}
