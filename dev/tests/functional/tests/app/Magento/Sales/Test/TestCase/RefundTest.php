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

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Sales\Test\Fixture\OrderCheckout;

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
