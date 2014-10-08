<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

/**
 * Interface for entities which can be extended with custom attributes.
 */
interface ExtensibleEntityInterface
{
    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Service\Data\AttributeValueInterface|null null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode);

    /**
     * Retrieve custom attributes values.
     *
     * @return \Magento\Framework\Service\Data\AttributeValueInterface[]
     */
    public function getCustomAttributes();
}
