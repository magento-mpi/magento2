<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Order\Shipment;

use Mtf\Block\Block;

/**
 * Class Items
 * Items block on shipment view page
 */
class Items extends Block
{
    /**
     * Grand total css selector
     *
     * @var string
     */
    protected $grandTotal = 'td.col.qty';

    /**
     * Get total qty
     *
     * @return int
     */
    public function getTotalQty()
    {
        return trim($this->_rootElement->find($this->grandTotal)->getText());
    }
}
