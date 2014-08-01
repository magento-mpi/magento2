<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page;

/**
 * Class CheckoutOnepage
 */
class CheckoutOnepage extends \Magento\Checkout\Test\Page\CheckoutOnepage
{
    const MCA = 'checkout/onepage/index';

    protected $_blocks = [
        'billingBlock' => [
            'name' => 'billingBlock',
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Onepage\Billing',
            'locator' => '#checkout-step-billing',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Onepage\Billing
     */
    public function getBillingBlock()
    {
        return $this->getBlockInstance('billingBlock');
    }
}
