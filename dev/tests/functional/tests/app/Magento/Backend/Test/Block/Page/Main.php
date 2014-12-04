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
     * Selector for prices.
     *
     * @var string
     */
    protected $priceBlock = '.price';

    /**
     * Get price block.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->_rootElement->find($this->priceBlock)->getText();
    }
}
