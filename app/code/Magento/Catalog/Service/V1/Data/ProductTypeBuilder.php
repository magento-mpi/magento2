<?php
/**
 * Builder for product type service data object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class ProductTypeBuilder extends AbstractObjectBuilder
{
    /**
     * Set product type name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(ProductType::NAME, $name);
    }

    /**
     * Set product type label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(ProductType::LABEL, $label);
    }
}
