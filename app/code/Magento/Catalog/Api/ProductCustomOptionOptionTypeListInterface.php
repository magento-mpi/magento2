<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * @todo Implementation \Magento\Catalog\Model\ProductOptions\Config
 */
interface ProductCustomOptionOptionTypeListInterface
{
    /**
     * Get custom option types
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface[]
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::getTypes - previous implementation
     */
    public function getItems();
}
