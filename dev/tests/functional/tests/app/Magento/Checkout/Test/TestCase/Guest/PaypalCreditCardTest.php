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

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Sales\Test\Fixture\OrderCheckout;

/**
 * Class PaypalCreditCardTest
 *
 * Test one page checkout with PayPal credit card payments (payments advanced and payflow link).
 *
 * @package Magento\Test\TestCase\Guest
 */
class PaypalCreditCardTest extends Functional
{
    /**
     * Guest checkout using PayPal payment method specified by the dataprovider.
     *
     * @param OrderCheckout $fixture
     * @dataProvider dataProviderCheckout
     *
     * @ZephyrId MAGETWO-12991, MAGETWO-12974
     */
    public function testOnepageCheckout(OrderCheckout $fixture)
    {
        $this->markTestSkipped('Bamboo inability to run tests on instance with public IP address');
        $fixture->persist();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');

        // Verify order in Backend
        /** @var string $orderId */
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);

        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        if ($fixture->getCommentHistory()) {
            $expectedAuthorizedAmount = $fixture->getCommentHistory();
        } else {
            $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getGrandTotal();
        }
        $this->assertContains(
            $expectedAuthorizedAmount,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }

    /**
     * Data providers for checking out
     *
     * @return array
     */
    public function dataProviderCheckout()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder())
        );
    }
}
