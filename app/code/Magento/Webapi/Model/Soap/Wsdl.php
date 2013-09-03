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
class Magento_Webapi_Model_Soap_Wsdl extends Wsdl
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
     * @param Magento_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased $strategy
     */
    public function __construct($name, $uri, Magento_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased $strategy)
    {
        $this->_uri = $uri;
        parent::__construct($name, $uri, $strategy);
    }
}
