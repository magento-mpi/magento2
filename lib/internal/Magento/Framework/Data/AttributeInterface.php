<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data;

/**
 * Interface for custom attribute value.
 */
interface AttributeInterface
{
    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode();

    /**
     * Get attribute value
     *
     * @return mixed
     */
    public function getValue();
}
