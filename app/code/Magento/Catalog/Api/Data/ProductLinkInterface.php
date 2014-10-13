<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Created from @see \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink
 */
interface ProductLinkInterface
{
    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Get product position
     *
     * @return int
     */
    public function getPosition();
}
