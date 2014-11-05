<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\PrintOrder;

use Mtf\Block\Block;

/**
 * Totals block on order's print page.
 */
class Totals extends Block
{
    /**
     * Grand total css selector.
     *
     * @var string
     */
    protected $grandTotal = '.grand_total span.price';

    /**
     * Get grand total price.
     *
     * @return string|null
     */
    public function getGrandTotal()
    {
        return $this->escapeCurrency($this->_rootElement->find($this->grandTotal)->getText());
    }

    /**
     * Method that escapes currency symbols.
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }
}
