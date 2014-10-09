<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Product\Attribute;

interface AttributeTypeInterface
{
    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option value
     *
     * @return string
     */
    public function getValue();
}
