<?php
namespace Magento\Catalog\Api\Data;

/**
 * Builder class for \Magento\Catalog\Api\Data\ProductLinkAttributeInterface
 */
class ProductLinkAttributeInterfaceBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkAttributeInterface::KEY, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkAttributeInterface::VALUE, $value);
    }
}
