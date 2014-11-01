<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

/**
 * Attribute metadata object builder interface.
 */
interface AttributeMetadataBuilderInterface
{
    /**
     * Set code of the attribute.
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode);

    /**
     * Build the attribute data object.
     *
     * @return AbstractSimpleObject
     */
    public function create();
}
