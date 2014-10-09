<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for entities which can be extended with custom attributes.
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
     * @param \Magento\Framework\Service\Data\AttributeValue[] $attributes
     * @return $this
     * @throws \LogicException If array elements are not of AttributeValue type
     */
    public function setCustomAttributes(array $attributes);

    /**
     * Return created DataInterface object
     *
     * @return ExtensibleDataInterface
     */
    public function create();
}
