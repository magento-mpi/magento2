<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

/**
 * Interface for entities which can be extended with custom attributes.
 */
interface ExtensibleDataInterface
{
    const CUSTOM_ATTRIBUTES = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Api\AttributeInterface|null null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode);

    /**
     * Retrieve custom attributes values.
     *
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getCustomAttributes();
}
