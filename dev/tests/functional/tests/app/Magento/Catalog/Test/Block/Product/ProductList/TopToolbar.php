<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Catalog\Test\Block\Product\ProductList;

use Mtf\Block\Block;

/**
 * Class TopToolbar
 * Top toolbar the product list page
 */
class TopToolbar extends Block
{
    /**
     * Selector for "sort by" element
     *
     * @var string
     */
    protected $sorter = '#sorter';

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
