<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface CategoryProductLinkInterface
{
    const SKU = 'sku';
    const POSITION = 'position';
    const CATEGORY_ID = 'category_id';

    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * Get category id
     *
     * @return int
     */
    public function getCategoryId();
}
