<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Wsdl;

/**
 * Factory of WSDL builders.
 */
class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create WSDL builder instance.
     *
     * @param string $wsdlName
     * @param string $endpointUrl
     * @return \Magento\Webapi\Model\Soap\Wsdl
     */
    public function create($wsdlName, $endpointUrl)
    {
        return $this->_objectManager->create(
            'Magento\Webapi\Model\Soap\Wsdl',
            array('name' => $wsdlName, 'uri' => $endpointUrl)
        );
    }
}
