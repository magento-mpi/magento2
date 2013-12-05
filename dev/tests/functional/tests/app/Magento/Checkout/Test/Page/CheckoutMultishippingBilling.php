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
 * Select payment method page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingBilling extends Page
{
    /**
     * URL for multishipping billing page
     */
    const MCA = 'checkout/multishipping/billing';

    /**
     * Billing block form
     *
     * @var string
     */
    protected $billingBlock = '#multishipping-billing-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get billing form
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Billing
     */
    public function getBillingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingBilling(
            $this->_browser->find($this->billingBlock, Locator::SELECTOR_CSS)
        );
    }
}
