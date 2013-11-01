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
class CheckoutOnepage extends Page
{
    /**
     * URL for one page checkout cart page
     */
    const MCA = 'checkout/onepage';

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Login
     */
    private $loginBlock;

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Billing
     */
    private $billingBlock;

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Shipping
     */
    private $shippingBlock;

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Shipping\Method
     */
    private $shippingMethodBlock;

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Payment\Methods
     */
    private $paymentMethodsBlock;

    /**
     * @var \Magento\Checkout\Test\Block\Onepage\Review
     */
    private $reviewBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        //Blocks
        $this->loginBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageLogin(
            $this->_browser->find('#checkout-step-login', Locator::SELECTOR_CSS));
        $this->billingBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageBilling(
            $this->_browser->find('#checkout-step-billing', Locator::SELECTOR_CSS));
        $this->shippingBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageShipping(
            $this->_browser->find('#checkout-step-shipping', Locator::SELECTOR_CSS));
        $this->shippingMethodBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageShippingMethod(
            $this->_browser->find('#checkout-step-shipping_method', Locator::SELECTOR_CSS));
        $this->paymentMethodsBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepagePaymentMethods(
            $this->_browser->find('#checkout-step-payment', Locator::SELECTOR_CSS));
        $this->reviewBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageReview(
            $this->_browser->find('#checkout-step-review', Locator::SELECTOR_CSS));
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Login
     */
    public function getLoginBlock()
    {
        return $this->loginBlock;
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Billing
     */
    public function getBillingBlock()
    {
        return $this->billingBlock;
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping
     */
    public function getShippingBlock()
    {
        return $this->shippingBlock;
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping\Method
     */
    public function getShippingMethodBlock()
    {
        return $this->shippingMethodBlock;
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Payment\Methods
     */
    public function getPaymentMethodsBlock()
    {
        return $this->paymentMethodsBlock;
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Review
     */
    public function getReviewBlock()
    {
        return $this->reviewBlock;
    }
}
