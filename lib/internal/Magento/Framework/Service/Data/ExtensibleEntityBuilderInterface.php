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
interface ExtensibleEntityBuilderInterface
{
    /**
     * Set custom attribute value.
     *
     * @param string $attributeCode
     * @param \Magento\Framework\Service\Data\AttributeValueInterface $attributeValue
     * @return $this
     */
    public function setCustomAttribute(
        $attributeCode,
        \Magento\Framework\Service\Data\AttributeValueInterface $attributeValue
    );

    /**
     * Set array of custom attributes
     *
     * @param \Magento\Framework\Service\Data\AttributeValue[] $attributes
     * @return $this
     * @throws \LogicException If array elements are not of AttributeValue type
     */
    public function setCustomAttributes(array $attributes);

    /**
     * Return created DataInterface object
     *
     * @return ExtensibleEntityInterface
     */
    public function create();
}
