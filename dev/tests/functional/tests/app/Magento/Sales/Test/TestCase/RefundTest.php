<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderCheckout;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class RefundTest
 */
class RefundTest extends Functional
{
    /**
     * Sets up the preconditions for the refund tests.
     *
     * @param OrderCheckout $fixture
     * @return void
     */
    public function setupPreconditions(OrderCheckout $fixture)
    {
        // Enable returns
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();

        // Create an order.
        $fixture->persist();

        // Log into the backend.
        Factory::getApp()->magentoBackendLoginUser();

        // Close the order.
        Factory::getApp()->magentoSalesCloseOrder($fixture);
    }
}
