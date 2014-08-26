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

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'centinelAuthenticationBlock' => [
            'class' => 'Magento\Centinel\Test\Block\Authentication',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'loginBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Login',
            'locator' => '#checkout-step-login',
            'strategy' => 'css selector',
        ],
        'billingBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Billing',
            'locator' => '#checkout-step-billing',
            'strategy' => 'css selector',
        ],
        'shippingBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Shipping',
            'locator' => '#checkout-step-shipping',
            'strategy' => 'css selector',
        ],
        'shippingMethodBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Shipping\Method',
            'locator' => '#checkout-step-shipping_method',
            'strategy' => 'css selector',
        ],
        'paymentMethodsBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Payment\Methods',
            'locator' => '#checkout-step-payment',
            'strategy' => 'css selector',
        ],
        'reviewBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Review',
            'locator' => '#checkout-step-review',
            'strategy' => 'css selector',
        ],
        'storeCreditBlock' => [
            'class' => 'Magento\CustomerBalance\Test\Block\Checkout\Onepage\Payment\Additional',
            'locator' => '#customerbalance-placer',
            'strategy' => 'css selector',
        ],
        'customerAttributeBillingBlock' => [
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Onepage\Billing',
            'locator' => '#checkout-step-billing',
            'strategy' => 'css selector',
        ],
        'payflowAdvancedCcBlock' => [
            'class' => 'Magento\Paypal\Test\Block\Form\PayflowAdvanced\CcAdvanced',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'payflowLinkCcBlock' => [
            'class' => 'Magento\Paypal\Test\Block\Form\PayflowAdvanced\CcLink',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'rewardPointsBlock' => [
            'class' => 'Magento\Reward\Test\Block\Checkout\Payment\Additional',
            'locator' => '#reward_placer',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Centinel\Test\Block\Authentication
     */
    public function getCentinelAuthenticationBlock()
    {
        return $this->getBlockInstance('centinelAuthenticationBlock');
    }

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
     * @return \Magento\CustomerBalance\Test\Block\Checkout\Onepage\Payment\Additional
     */
    public function getStoreCreditBlock()
    {
        return $this->getBlockInstance('storeCreditBlock');
    }

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Onepage\Billing
     */
    public function getCustomerAttributeBillingBlock()
    {
        return $this->getBlockInstance('customerAttributeBillingBlock');
    }

    /**
     * @return \Magento\Paypal\Test\Block\Form\PayflowAdvanced\CcAdvanced
     */
    public function getPayflowAdvancedCcBlock()
    {
        return $this->getBlockInstance('payflowAdvancedCcBlock');
    }

    /**
     * @return \Magento\Paypal\Test\Block\Form\PayflowAdvanced\CcLink
     */
    public function getPayflowLinkCcBlock()
    {
        return $this->getBlockInstance('payflowLinkCcBlock');
    }

    /**
     * @return \Magento\Reward\Test\Block\Checkout\Payment\Additional
     */
    public function getRewardPointsBlock()
    {
        return $this->getBlockInstance('rewardPointsBlock');
    }
}
