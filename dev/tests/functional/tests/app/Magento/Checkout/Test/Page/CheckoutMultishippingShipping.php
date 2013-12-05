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
     * @var string
     */
    protected $shippingBlock = '#shipping_method_form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get shipping block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingShipping(
            $this->_browser->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }
}
