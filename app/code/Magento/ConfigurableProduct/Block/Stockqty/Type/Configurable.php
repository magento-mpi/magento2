<?php
/**
 * Product stock qty block for configurable product type
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Block\Stockqty\Type;

use Magento\Catalog\Model\Product;

class Configurable extends \Magento\CatalogInventory\Block\Stockqty\Composite
{
    /**
     * Retrieve child products
     *
     * @return Product[]
     */
    protected function _getChildProducts()
    {
        return $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct());
    }
}
