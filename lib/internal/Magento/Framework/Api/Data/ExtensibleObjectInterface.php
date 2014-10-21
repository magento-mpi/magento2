<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api\Data;

interface ExtensibleObjectInterface extends SimpleObjectInterface
{
    const CUSTOM_ATTRIBUTE = 'custom_attribute';
    const CUSTOM_ATTRIBUTES = 'custom_attributes';

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Service\Data\AttributeValue|null The value. Null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode);

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return \Magento\Framework\Service\Data\AttributeValue[]|null
     */
    public function getCustomAttributes();
} 
