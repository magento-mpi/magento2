<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Catalog\Product\View\Type;

use Mtf\Block\Block;

/**
 * Class Grouped
 * Grouped product blocks on frontend
 */
class Grouped extends Block
{
    /**
     * Selector qty for sub product
     *
     * @var string
     */
    protected $qtySubProduct = '[name="super_group[%d]"]';

    /**
     * Get qty for subProduct
     *
     * @param int $subProductId
     * @return string
     */
    public function getQty($subProductId)
    {
        return $this->_rootElement->find(sprintf($this->qtySubProduct, $subProductId))->getValue();
    }
}
