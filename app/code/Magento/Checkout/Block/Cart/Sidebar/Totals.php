<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart\Sidebar;

use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Framework\View\Block\IdentityInterface;

/**
 * Sidebar totals block
 */
class Totals extends AbstractCart
{
    /**
     * Get shopping cart subtotal.
     *
      * @return  float
     */
    public function getSubtotal()
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            $subtotal = $totals['subtotal']->getValue();
        }
        return $subtotal;
    }
}
