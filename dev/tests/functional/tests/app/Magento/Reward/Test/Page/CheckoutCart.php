<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page;

use Magento\Checkout\Test\Page\CheckoutCart as AbstractCheckoutCart;

/**
 * Class CheckoutCart
 * Page of checkout page
 */
class CheckoutCart extends AbstractCheckoutCart
{
    const MCA = 'reward_checkout/cart/index';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['checkoutTooltipBlock'] = [
            'name' => 'checkoutTooltipBlock',
            'class' => 'Magento\Reward\Test\Block\Tooltip\Checkout',
            'locator' => '.rewards',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Reward\Test\Block\Tooltip\Checkout
     */
    public function getCheckoutTooltipBlock()
    {
        return $this->getBlockInstance('checkoutTooltipBlock');
    }
}
