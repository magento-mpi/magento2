<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Order\PrintOrder;

use Magento\Sales\Test\Block\Order\PrintOrder\Totals;

/**
 * Class SalesRule
 * Sales Rule total block in print order.
 */
class SalesRule extends Totals
{
    /**
     * Sales Rule selector.
     *
     * @var string
     */
    protected $salesRuleSelector = '.amount > span.price';

    /**
     * Get sales rule discount.
     *
     * @return string
     */
    public function getSalesRuleDiscount()
    {
        return $this->escapeCurrency($this->_rootElement->find($this->salesRuleSelector)->getText());
    }

    /**
     * Escape currency in price.
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
