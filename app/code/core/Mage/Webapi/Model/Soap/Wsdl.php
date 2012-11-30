<?php
use Zend\Soap\Wsdl;

/**
 * Magento-specific WSDL builder.
 *
 * @copyright {}
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
     * @param Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased $strategy
     */
    public function __construct($name, $uri, Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased $strategy)
    {
        $this->_uri = $uri;
        parent::__construct($name, $uri, $strategy);
    }
}
