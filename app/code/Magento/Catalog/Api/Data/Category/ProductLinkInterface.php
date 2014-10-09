<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\Category;

/**
 * Implementation
 * @see \Magento\Catalog\Model\Product
 */
interface ProductLinkInterface
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
