<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CheckoutCart
 * Checkout cart page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutCart extends Page
{
    /**
     * URL for checkout cart page
     */
    const MCA = 'checkout/cart';

    /**
     * Cart shipping block
     *
     * @var string
     */
    protected $shippingBlock = '.block.shipping';

    /**
     * Cart totals block
     *
     * @var string
     */
    protected $totalsBlock = '#shopping-cart-totals-table';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get shopping cart block
     *
     * @return \Magento\Checkout\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutCart(
            $this->_browser->find('//div[contains(@class, "cart container")]', Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Get estimated shipping block
     *
     * @return \Magento\Checkout\Test\Block\Cart\Shipping
     */
    public function getEstimatedShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutCartShipping(
            $this->_browser->find('.block.shipping > div')
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('.messages .messages', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get cart shipping block
     *
     * @return \Magento\Checkout\Test\Block\Cart\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutCartShipping(
            $this->_browser->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get cart totals block
     *
     * @return \Magento\Checkout\Test\Block\Cart\Totals
     */
    public function getTotalsBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutCartTotals(
            $this->_browser->find($this->totalsBlock, Locator::SELECTOR_CSS)
        );
    }
}
