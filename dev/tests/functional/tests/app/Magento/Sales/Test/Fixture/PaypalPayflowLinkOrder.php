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
 * Class PaypalPayflowLinkOrder
 * Guest checkout using PayPal Payflow Link method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalPayflowLinkOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payflow Link.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalPayflowLink();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$168.72'
            )
        );
    }

    /**
     * Override persist to capture credit card data for Paypal Payflow Link payment method.
     */
    public function persist()
    {
        parent::persist();

        /** @var \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc $formBlock */
        $formBlock = Factory::getPageFactory()->getCheckoutOnepage()->getPayflowLinkCcBlock();
        $formBlock->fill($this->checkoutFixture->getCreditCard());
        $formBlock->pressContinue();
        $checkoutOnePageSuccess = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->orderId = $checkoutOnePageSuccess->getSuccessBlock()->getOrderId($this);
    }
}
