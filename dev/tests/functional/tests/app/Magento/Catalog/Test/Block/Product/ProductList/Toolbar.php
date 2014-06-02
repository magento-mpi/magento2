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
     * Selector for "sort by" element
     *
     * @var string
     */
    protected $sorter = '#sorter';

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

    /**
     * Get method of sorting product
     *
     * @return array|string
     */
    public function getSelectSortType()
    {
        return $this->_rootElement->find($this->sorter)->getValue();
    }

    /**
     * Get all available method of sorting product
     *
     * @return array|string
     */
    public function getSortType()
    {
        $content = str_replace("\r", '', $this->_rootElement->find($this->sorter)->getText());
        return explode("\n", $content);
    }
} 