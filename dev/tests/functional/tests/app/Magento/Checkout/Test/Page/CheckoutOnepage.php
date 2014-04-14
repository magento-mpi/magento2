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
     * One page checkout status login block
     *
     * @var string
     */
    protected $loginBlock = '#checkout-step-login';

    /**
     * One page checkout status billing block
     *
     * @var string
     */
    protected $billingBlock = '#checkout-step-billing';

    /**
     * One page checkout status shipping block
     *
     * @var string
     */
    protected $shippingBlock = '#checkout-step-shipping';

    /**
     * One page checkout status shipping method block
     *
     * @var string
     */
    protected $shippingMethodBlock = '#checkout-step-shipping_method';

    /**
     * One page checkout status payment method block
     *
     * @var string
     */
    protected $paymentMethodsBlock = '#checkout-step-payment';

    /**
     * One page checkout status review block
     *
     * @var string
     */
    protected $reviewBlock = '#checkout-step-review';

    /**
     * iFrame body locator
     *
     * @var string
     */
    protected $iframeBody = 'body';

    /**
     * 3D Secure frame locator
     *
     * @var Locator
     */
    protected $centinelFrame = '#centinel-authenticate-iframe';

    /**
     * Payflow Advanced iFrame locator
     *
     * @var $Locator
     */
    protected $payflowAdvancedFrame = "#payflow-advanced-iframe";

    /**
     * Payflow Link iFrame locator
     *
     * @var string
     */
    protected $payflowLinkFrame = "#payflow-link-iframe";

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get one page checkout status login block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Login
     */
    public function getLoginBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageLogin(
            $this->_browser->find($this->loginBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get one page checkout status billing block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Billing
     */
    public function getBillingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageBilling(
            $this->_browser->find($this->billingBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get one page checkout status shipping method block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageShipping(
            $this->_browser->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping\Method
     */
    public function getShippingMethodBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageShippingMethod(
            $this->_browser->find($this->shippingMethodBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get one page checkout status payment method block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Payment\Methods
     */
    public function getPaymentMethodsBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepagePaymentMethods(
            $this->_browser->find($this->paymentMethodsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get one page checkout status review block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Review
     */
    public function getReviewBlock()
    {
        $this->_browser->switchToFrame();
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageReview(
            $this->_browser->find($this->reviewBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get centinel card verification block
     *
     * @return \Magento\Centinel\Test\Block\Authentication
     */
    public function getCentinelAuthenticationBlock()
    {
        $this->_browser->switchToFrame(new Locator($this->centinelFrame));
        return Factory::getBlockFactory()->getMagentoCentinelAuthentication(
            $this->_browser->find($this->iframeBody, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Paypal Payment Advanced (payflow advanced) iframe.
     *
     * @return \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc
     */
    public function getPayflowAdvancedCcBlock()
    {
        $this->_browser->switchToFrame(new Locator($this->payflowAdvancedFrame));
        return Factory::getBlockFactory()->getMagentoPaymentFormPayflowAdvancedCc(
            $this->_browser->find($this->iframeBody, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Payflow Link iframe.
     *
     * @return \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc
     */
    public function getPayflowLinkCcBlock()
    {
        $this->_browser->switchToFrame(new Locator($this->payflowLinkFrame));
        return Factory::getBlockFactory()->getMagentoPaymentFormPayflowAdvancedCc(
            $this->_browser->find($this->iframeBody, Locator::SELECTOR_CSS)
        );
    }

}
