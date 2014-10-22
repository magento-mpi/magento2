<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

/**
 * Base builder interface for \Magento\Framework\Api\Data\ExtensibleDataInterface types.
 */
interface ExtensibleDataBuilderInterface
{
    /**
     * Set custom attribute value.
     *
     * @param \Magento\Framework\Api\Data\AttributeInterface $attribute
     * @return $this
     */
    public function setCustomAttribute(\Magento\Framework\Api\Data\AttributeInterface $attribute);

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
     * @return \Magento\Framework\Api\Data\ExtensibleDataInterface
     */
    public function create();
}
