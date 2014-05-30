<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\ProductList;

use Mtf\Block\Block;

/**
 * Class ProductPagination
 * Toolbar the product list page
 */
class Toolbar extends Block
{
    /**
     * Selector next active element
     *
     * @var string
     */
    protected $nextPageSelector = '.item.current + .item a';

    /**
     * Go to the next page
     *
     * @return bool
     */
    public function nextPage()
    {
        $nextPageItem = $this->_rootElement->find($this->nextPageSelector);
        if ($nextPageItem->isVisible()) {
            $nextPageItem->click();
            return true;
        }

        return false;
    }
} 