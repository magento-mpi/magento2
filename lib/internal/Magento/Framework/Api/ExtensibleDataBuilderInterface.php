<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

/**
 * Base builder interface for \Magento\Framework\Api\ExtensibleDataInterface types.
 */
interface ExtensibleDataBuilderInterface
{
    /**
     * Set custom attribute value.
     *
     * @param \Magento\Framework\Api\AttributeInterface $attribute
     * @return $this
     */
    public function setCustomAttribute(\Magento\Framework\Api\AttributeInterface $attribute);

    /**
     * Set array of custom attributes
     *
     * @param \Magento\Framework\Api\AttributeValue[] $attributes
     * @return $this
     * @throws \LogicException If array elements are not of AttributeValue type
     */
    public function setCustomAttributes(array $attributes);

    /**
     * Return created DataInterface object
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface
     */
    public function create();
}
