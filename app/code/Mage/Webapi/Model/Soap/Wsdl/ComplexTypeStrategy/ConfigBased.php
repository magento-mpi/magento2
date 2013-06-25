<?php
use Zend\Soap\Wsdl\ComplexTypeStrategy\AbstractComplexTypeStrategy,
    Zend\Soap\Wsdl\ComplexTypeStrategy\ComplexTypeStrategyInterface,
    Zend\Soap\Wsdl;

/**
 * Magento-specific Complex type strategy for WSDL auto discovery.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** TODO: Remove mention about config from class name */
class Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased extends AbstractComplexTypeStrategy
{
    /**
     * Inject XSD describing service method input/output directly into WSDL.
     *
     * @param DOMNode $complexTypeNode DOMNode to be added to the WSDL
     * @return string
     */
    public function addComplexType($complexTypeNode)
    {
        $complexType = $this->getContext()->toDomDocument()->importNode($complexTypeNode, true);
        $this->getContext()->getSchema()->appendChild($complexType);
        //TODO: Fix return
        return $complexTypeNode;
    }
}
