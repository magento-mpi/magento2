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
use Magento\Checkout\Test\Block\Multishipping;

/**
 * Class CheckoutMultishippingShipping
 * Select shipping methods page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingShipping extends Page
{
    /**
     * URL for shipping page
     */
    const MCA = 'checkout/multishipping/shipping';

    /**
     * Mustishipping checkout shipping
     *
     * @var Multishipping\Shipping
     * @private
     */
    private $shippingBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->shippingBlock = Factory::getBlockFactory()->getMagentoCheckoutMultishippingShipping(
            $this->_browser->find('#shipping_method_form', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get shipping block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Shipping
     */
    public function getShippingBlock()
    {
        return $this->shippingBlock;
    }
}
