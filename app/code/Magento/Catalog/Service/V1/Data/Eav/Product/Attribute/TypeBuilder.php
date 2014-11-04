<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Product\Attribute;

/**
 * Class TypeBuilder
 *
 * @package Magento\Catalog\Service\V1\Data\Eav\Product\Attribute
 * @codeCoverageIgnore
 */
class TypeBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set option label
     *
     * @param  string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(Type::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @param  string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(Type::VALUE, $value);
    }
}
