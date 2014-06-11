<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

/**
 * Class ProductAttributeTypeBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class ProductAttributeTypeBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set option label
     *
     * @param  string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(ProductAttributeType::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @param  string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(ProductAttributeType::VALUE, $value);
    }
}
