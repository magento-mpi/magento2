<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Block\Widget\Search;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Result
 * Wish list search result block
 */
class Result extends Block
{
    /**
     * Search row css selector
     *
     * @var string
     */
    protected $searchRow = '//td[contains(@class,"col list")][.="%s"]';

    /**
     * Wish list is visible in grid
     *
     * @param string $name
     * @return bool
     */
    public function isWishlistVisibleInGrid($name)
    {
        return $this->_rootElement->find(sprintf($this->searchRow, $name), Locator::SELECTOR_XPATH)->isVisible();
    }
}
