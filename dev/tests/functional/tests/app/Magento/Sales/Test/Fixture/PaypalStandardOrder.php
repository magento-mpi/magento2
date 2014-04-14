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
 * Class PaypalStandardOrder
 * Guest checkout using PayPal Payments Standard method
 *
 * @package Magento\Sales\Test\Fixture
 */
class PaypalStandardOrder extends OrderCheckout
{
    /**
     * Prepare data for guest checkout using Paypal Payments Standard.
     */
    protected function _initData()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalStandard();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        parent::persist();

        //PayPal Site
        $fixture = $this->checkoutFixture;
        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getBillingBlock()->clickLoginLink();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();
        $paypalPage->getMainPanelBlock()->clickReturnLink();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->orderId = $successPage->getSuccessBlock()->getOrderId($fixture);
    }
}
