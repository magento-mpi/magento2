<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category;

class ProductLinkBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        $this->_set(ProductLink::SKU, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        $this->_set(ProductLink::POSITION, $value);
        return $this;
    }
}
