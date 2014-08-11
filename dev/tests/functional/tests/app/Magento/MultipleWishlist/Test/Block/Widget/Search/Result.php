<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Search button button css selector
     *
     * @var string
     */
    protected $searchRow = '//td[contains(@class,"col list")][.="%s"]';

    /**
     * Visible in grid
     *
     * @param string $name
     * @return bool
     */
    public function visibleInGrid($name)
    {
        return $this->_rootElement->find(sprintf($this->searchRow, $name), Locator::SELECTOR_XPATH)->isVisible();
    }
}
