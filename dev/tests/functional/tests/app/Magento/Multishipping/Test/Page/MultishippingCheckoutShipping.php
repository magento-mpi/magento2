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

namespace Magento\Multishipping\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * class MultishippingCheckoutShipping
 * Select shipping methods page
 *
 * @package Magento\Multishipping\Test\Page
 */
class MultishippingCheckoutShipping extends Page
{
    /**
     * URL for shipping page
     */
    const MCA = 'multishipping/checkout/shipping';

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
     * @return \Magento\Multishipping\Test\Block\Checkout\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutShipping(
            $this->_browser->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }
}
