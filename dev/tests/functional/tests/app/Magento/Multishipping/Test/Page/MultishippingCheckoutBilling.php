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
 * Select payment method page
 *
 * @package Magento\Multishipping\Test\Page
 */
class MultishippingCheckoutBilling extends Page
{
    /**
     * URL for multishipping billing page
     */
    const MCA = 'multishipping/checkout/billing';

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
     * @return \Magento\Multishipping\Test\Block\Checkout\Billing
     */
    public function getBillingBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutBilling(
            $this->_browser->find($this->billingBlock, Locator::SELECTOR_CSS)
        );
    }
}
