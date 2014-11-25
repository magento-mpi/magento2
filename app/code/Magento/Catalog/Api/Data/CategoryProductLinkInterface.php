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
     * @return string
     */
    public function getCategoryId();
}
