<?php
use \Zend\Soap\Wsdl;

/**
 * Magento-specific WSDL builder.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

class Wsdl extends \Zend\Soap\Wsdl
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
     * @param string|\Zend\Uri\Uri $uri
     * @param \Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy\ConfigBased $strategy
     */
    public function __construct($name, $uri, \Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy\ConfigBased $strategy)
    {
        $this->_uri = $uri;
        parent::__construct($name, $uri, $strategy);
    }
}
