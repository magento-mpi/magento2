<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CheckoutOnepage
 */
class CheckoutOnepage extends FrontendPage
{
    const MCA = 'checkout/onepage';

    protected $_blocks = [
        'loginBlock' => [
            'name' => 'loginBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Login',
            'locator' => '#checkout-step-login',
            'strategy' => 'css selector',
        ],
        'billingBlock' => [
            'name' => 'billingBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Billing',
            'locator' => '#checkout-step-billing',
            'strategy' => 'css selector',
        ],
        'shippingBlock' => [
            'name' => 'shippingBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Shipping',
            'locator' => '#checkout-step-shipping',
            'strategy' => 'css selector',
        ],
        'shippingMethodBlock' => [
            'name' => 'shippingMethodBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Shipping\Method',
            'locator' => '#checkout-step-shipping_method',
            'strategy' => 'css selector',
        ],
        'paymentMethodsBlock' => [
            'name' => 'paymentMethodsBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Payment\Methods',
            'locator' => '#checkout-step-payment',
            'strategy' => 'css selector',
        ],
        'reviewBlock' => [
            'name' => 'reviewBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Review',
            'locator' => '#checkout-step-review',
            'strategy' => 'css selector',
        ],
        'centinelAuthenticationBlock' => [
            'name' => 'centinelAuthenticationBlock',
            'class' => 'Magento\Centinel\Test\Block\Authentication',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'payflowAdvancedCcBlock' => [
            'name' => 'payflowAdvancedCcBlock',
            'class' => 'Magento\Payment\Test\Block\Form\PayflowAdvanced\CcAdvanced',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'payflowLinkCcBlock' => [
            'name' => 'payflowLinkCcBlock',
            'class' => 'Magento\Payment\Test\Block\Form\PayflowAdvanced\CcLink',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Login
     */
    public function getLoginBlock()
    {
        return $this->getBlockInstance('loginBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Billing
     */
    public function getBillingBlock()
    {
        return $this->getBlockInstance('billingBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping
     */
    public function getShippingBlock()
    {
        return $this->getBlockInstance('shippingBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Shipping\Method
     */
    public function getShippingMethodBlock()
    {
        return $this->getBlockInstance('shippingMethodBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Payment\Methods
     */
    public function getPaymentMethodsBlock()
    {
        return $this->getBlockInstance('paymentMethodsBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Review
     */
    public function getReviewBlock()
    {
        return $this->getBlockInstance('reviewBlock');
    }

    /**
     * @return \Magento\Centinel\Test\Block\Authentication
     */
    public function getCentinelAuthenticationBlock()
    {
        return $this->getBlockInstance('centinelAuthenticationBlock');
    }

    /**
     * @return \Magento\Payment\Test\Block\Form\PayflowAdvanced\CcAdvanced
     */
    public function getPayflowAdvancedCcBlock()
    {
        return $this->getBlockInstance('payflowAdvancedCcBlock');
    }

    /**
     * @return \Magento\Payment\Test\Block\Form\PayflowAdvanced\CcLink
     */
    public function getPayflowLinkCcBlock()
    {
        return $this->getBlockInstance('payflowLinkCcBlock');
    }
}
