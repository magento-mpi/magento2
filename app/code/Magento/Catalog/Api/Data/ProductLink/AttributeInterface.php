<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\ProductLink;

/**
 * Implementation: create new model \Magento\Catalog\Model\ProductLink\Attribute
 * @todo extend from Framework\KeyValueInterface
 */
interface AttributeInterface
{
    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get attribute type
     *
     * @return string
     */
    public function getType();
}
