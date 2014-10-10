<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\ProductLink;

/**
 * Implementation: create new model \Magento\Catalog\Model\ProductLink\Type
 * @todo extend from Framework\KeyValueInterface
 */
interface TypeInterface
{
    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get code
     *
     * @return int
     */
    public function getCode();
}
