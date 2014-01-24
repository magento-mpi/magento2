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

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class PaypalPaymentsAdvancedOrder
 * Guest checkout using PayPal Payments Advanced method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalPaymentsAdvancedOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payments Advanced.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalAdvanced();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }

    /**
     * Override persist to capture credit card data for Paypal Payments Advanced payment method.
     */
    public function persist()
    {
        parent::persist();

        /** @var \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc $formBlock */
        $formBlock = Factory::getPageFactory()->getCheckoutOnepage()->getPayflowAdvancedCcBlock();
        $formBlock->fill($this->checkoutFixture);
        $formBlock->pressContinue();

        $checkoutOnePageSuccess = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $checkoutOnePageSuccess->waitForSuccessBlockVisible();
        $this->orderId = $checkoutOnePageSuccess->getSuccessBlock()->getOrderId($this);
    }
}