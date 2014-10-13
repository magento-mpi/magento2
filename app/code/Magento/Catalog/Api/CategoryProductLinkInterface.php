<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Implementation
 * @see \Magento\Catalog\Model\Product
 */
interface CategoryProductLinkInterface
{
    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @return int|null
     */
    public function getPosition();
}
