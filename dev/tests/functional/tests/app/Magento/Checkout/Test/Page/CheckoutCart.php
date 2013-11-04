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
use Magento\Checkout\Test\Block\Cart;

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
     * Shopping cart block
     *
     * @var Cart
     */
    private $cartBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->cartBlock = Factory::getBlockFactory()->getMagentoCheckoutCart(
            $this->_browser->find('//div[contains(@class, "cart container")]', Locator::SELECTOR_XPATH));

    }

    /**
     * Get shopping cart block
     *
     * @return Cart
     */
    public function getCartBlock()
    {
        return $this->cartBlock;
    }
}
