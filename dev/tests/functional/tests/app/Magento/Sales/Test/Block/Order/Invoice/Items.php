<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\Invoice;

use Mtf\Block\Block;

/**
 * Class Items
 * Items block on invoice view page
 */
class Items extends Block
{
    /**
     * Grand total css selector
     *
     * @var string
     */
    protected $grandTotal = '.grand_total span.price';

    /**
     * Get grand total price
     *
     * @param string $currency [optional]
     * @return string
     */
    public function getGrandTotal($currency = '$')
    {
        return trim($this->_rootElement->find($this->grandTotal)->getText(), ' ' . $currency);
    }
}
