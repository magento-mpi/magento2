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
     * @var Multishipping\Billing
     */
    private $billingBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->billingBlock = Factory::getBlockFactory()->getMagentoCheckoutMultishippingBilling(
            $this->_browser->find('#multishipping-billing-form', Locator::SELECTOR_CSS));
    }

    /**
     * Get billing form
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Billing
     */
    public function getBillingBlock()
    {
        return $this->billingBlock;
    }
}
