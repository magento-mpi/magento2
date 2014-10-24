<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Test\Block\Cart;

use Magento\Checkout\Test\Block\Cart\Totals;
use Mtf\Client\Element\Locator;

/**
 * Class TotalsFpt
 * Cart totals fpt block
 */
class TotalsFpt extends Totals
{
    /**
     * FPT totals locator
     *
     * @var string
     */
    protected $totalFpt = '.price';

    /**
     * Get FPT Total Text
     *
     * @return string
     */
    public function getTotalFpt()
    {
        $grandTotal = $this->_rootElement->find($this->totalFpt, Locator::SELECTOR_CSS)->getText();
        return $this->escapeCurrency($grandTotal);
    }
}
