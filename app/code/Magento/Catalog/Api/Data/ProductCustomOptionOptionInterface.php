<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option - previous implementation
 * @todo \Magento\Catalog\Model\Product\Option implements
 */
interface ProductCustomOptionOptionInterface
{
    /**
     * Get product SKU
     *
     * @return string
     */
    public function getProductSKU();

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get option type
     *
     * @return string
     */
    public function getType();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get is require
     *
     * @return bool
     */
    public function getIsRequire();

    /**
     * Get option metadata
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionAttributeInterface[]
     */
    public function getMetadata();
}
