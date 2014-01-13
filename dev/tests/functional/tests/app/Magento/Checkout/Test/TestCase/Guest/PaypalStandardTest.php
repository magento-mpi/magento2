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

/**
 * Class OnepageTest
 * Test one page with PayPal Standard payment method
 *
 * @package Magento\Test\TestCase\Guest
 */
class PaypalStandardTest extends Functional
{
    /**
     * Guest checkout using PayPal Payments Standard method and offline shipping method
     *
     * @ZephyrId MAGETWO-12964
     */
    public function testOnepageCheckout()
    {
        $this->markTestSkipped('1. Bamboo inability to run tests on instance without public IP address. '
        .'2. Blocked by MAGETWO-19364');

        /** @var \Magento\Sales\Test\Fixture\PaypalStandardOrder $fixture */
        $fixture = Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder();
        $fixture->persist();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);

        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');

        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getCheckoutFixture()->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        if ($fixture->getCheckoutFixture()->getCommentHistory()) {
            $expectedAuthorizedAmount = $fixture->getCheckoutFixture()->getCommentHistory();
        } else {
            $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getCheckoutFixture()->getGrandTotal();
        }
        $this->assertContains(
            $expectedAuthorizedAmount,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
