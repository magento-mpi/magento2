<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Webapi\Model\Soap\Wsdl;

/**
 * Factory of WSDL builders.
 */
class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
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
            ['name' => $wsdlName, 'uri' => $endpointUrl]
        );
    }
}
