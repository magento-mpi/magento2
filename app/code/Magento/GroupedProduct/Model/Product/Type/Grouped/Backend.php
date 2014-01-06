<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Type\Grouped;

/**
 * Grouped product type implementation for backend
 */
class Backend extends \Magento\Catalog\Model\Product\Type\Grouped
{
    /**
     * No filters required in backend
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Type\Grouped
     */
    public function setSaleableStatus($product)
    {
        return $this;
    }
}
