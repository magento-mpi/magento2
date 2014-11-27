<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductCustomOptionOptionTypeListInterface
{
    /**
     * Get custom option types
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterface[]
     */
    public function getItems();
}
