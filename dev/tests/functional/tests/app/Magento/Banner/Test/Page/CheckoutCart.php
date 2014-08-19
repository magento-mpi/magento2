<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page;

/**
 * Class CheckoutCart
 * Shopping Cart Page
 */
class CheckoutCart extends \Magento\Checkout\Test\Page\CheckoutCart
{
    const MCA = 'banner/checkout/cart';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['cartBlock'] = [
            'name' => 'cartBlock',
            'class' => 'Magento\Banner\Test\Block\Cart',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Banner\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }
}
