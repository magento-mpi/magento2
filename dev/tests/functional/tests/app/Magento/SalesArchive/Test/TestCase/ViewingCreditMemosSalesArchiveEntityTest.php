<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for ViewingCreditMemosSalesArchiveEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Enable "Orders Archiving" in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Place order
 * 6. Create Invoice and Shipment
 *
 * Steps:
 * 1. Create Credit Memo
 * 2. Move order to Archive
 * 3. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28892
 */
class ViewingCreditMemosSalesArchiveEntityTest extends MoveToArchiveTest
{
    /**
     * View Credit Memos Sales Archive test
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @param string $configArchive
     * @return array
     */
    public function test(OrderInjectable $order, $steps, $configArchive)
    {
        // Steps:
        $ids = parent::test($order, $steps, $configArchive);

        return $ids;
    }
}
