<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Page;

use Mtf\Block\Block;

/**
 * Main block.
 */
class Main extends Block
{
    /**
     * Selector for Revenue prices.
     *
     * @var string
     */
    protected $revenuePriceBlock = '.dashboard-totals-list li:first-child .price';

    /**
     * Get Revenue price block.
     *
     * @return string
     */
    public function getRevenuePrice()
    {
        return $this->_rootElement->find($this->revenuePriceBlock)->getText();
    }
}
