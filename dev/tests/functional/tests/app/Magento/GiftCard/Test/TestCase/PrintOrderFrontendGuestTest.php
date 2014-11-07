<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Create products.
 * 2. Enable all Gift Options.
 * 3. Create Gift Card Account with Balance = 1.
 * 4. Create Customer Account.
 * 5. Place order with options according to dataSet.
 *
 * Steps:
 * 1. Find the Order on frontend.
 * 2. Navigate to: Orders and Returns.
 * 3. Fill the form with correspondent Order data.
 * 4. Click on the "Continue" button.
 * 5. Click on the "Print Order" button.
 * 6. Perform appropriate assertions.v
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-30253
 */
class PrintOrderFrontendGuestTest extends \Magento\Sales\Test\TestCase\PrintOrderFrontendGuestTest
{
    //
}
