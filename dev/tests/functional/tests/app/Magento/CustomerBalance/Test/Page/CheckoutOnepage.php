<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Page;

use Magento\Checkout\Test\Page\CheckoutOnepage as AbstractCheckoutOnepage;

/**
 * Class CheckoutOnepage
 * Page of checkout onepage
 */
class CheckoutOnepage extends AbstractCheckoutOnepage
{
    const MCA = 'customer_balance_checkout/onepage';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['storeCreditBlock'] = [
            'name' => 'storeCreditBlock',
            'class' => 'Magento\CustomerBalance\Test\Block\Checkout\Onepage\Payment\Additional',
            'locator' => '#customerbalance-placer',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\CustomerBalance\Test\Block\Checkout\Onepage\Payment\Additional
     */
    public function getStoreCreditBlock()
    {
        return $this->getBlockInstance('storeCreditBlock');
    }
}
