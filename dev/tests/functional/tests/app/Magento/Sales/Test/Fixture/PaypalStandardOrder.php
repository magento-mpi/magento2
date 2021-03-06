<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class PaypalStandardOrder
 * Guest checkout using PayPal Payments Standard method
 *
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
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
            ],
        ];
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
