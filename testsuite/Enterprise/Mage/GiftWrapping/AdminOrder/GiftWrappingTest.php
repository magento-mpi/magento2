<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for invoice, shipment and credit memo with gift options
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_AdminOrder_GiftWrappingTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    /**
     * Create Simple Product for tests
     *
     * @return string
     * @test
     */
    public function createSimpleProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_sku'];
    }

    /**
     * Create Gift Wrapping for tests
     *
     * @return array $gwData
     * @test
     */
    public function createGiftWrappingMain()
    {
        //Data
        $gwData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($gwData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $gwData;
    }

    /**
     * Create additional Gift Wrapping for tests
     *
     * @return array $gwData
     * @test
     */
    public function createGiftWrappingAdditional()
    {
        //Data
        $gwData = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($gwData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $gwData;
    }

    /**
     * <p>TL-MAGE-828: Gift Message for entire Order is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "Yes";</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Select any shipping method(for example: Free Shipping), any payment method</p>
     * <p>(for example: Check/Money order), shipping/billing addresses";</p>
     * <p>5. Look at Gift Options block of create Order page;</p>
     * <p>6. Fill the fields related to Gift Message for Entire Order (From/To/Message);</p>
     * <p>7. Click "Submit Order" button. When Order is placed, look at Gift Options block;</p>
     * <p>Expected result:</p>
     * <p>Prompt for entering Gift Message for Entire Order should be present there with From/To/Message fields;</p>
     * <p>Order is placed, Order View page opened. Gift options block should contain information about Gift Message,</p>
     * <p>with the same data (From/To/Message), as specified at Step 6;</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessagePerOrderAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftMessage($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-968:Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Look at Gift Options block of create Order page</p>
     * <p>Expected result:</p>
     * <p>In "Gift Options" block of Order page Gift Message prompt for entire Order should be absent</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessagePerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertTrue($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'), 'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-834:Gift Wrapping for entire Order is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "No"</p>
     * <p>2. At least one Gift Wrapping is created and enabled (for example, with Price $10 and image specified)</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Add any product to Items Ordered list (for example: Simple Product)</p>
     * <p>4. Select any shipping method (for example: Free Shipping), any payment method</p>
     * <p>(for example: Check/Money order), shipping/billing addresses</p>
     * <p>5. Look at Gift Options block of create Order page</p>
     * <p>6. In Gift Options block with "Gift Wrapping Design" dropdown - select Gift Wrapping (from Preconditions)
     * <p>7. Click "Submit Order" button. When Order is placed, look at Gift Options block</p>
     * <p>Expected result:</p>
     * <p>5. Dropdown for Gift Wrapping selection with title "Gift Wrapping Design" is present on "Gift Options"</p>
     * <p>block of page;</p>
     * <p>6. Image and price for selected Gift Wrapping should appear  below dropdown ("Price: $10.00" </p>
     * <p>in this example) and in Order Totals block ("Gift Wrapping for Order: $10.00" in this example);</p>
     * <p>7. Order should be placed placed, Order View page should be opened. Order Totals block of page should</p>
     * <p>contain information about Gift Wrapping for entire Order with price ("Gift Wrapping for Order: $10.00"</p>
     * <p>in this example) and corresponding image</p>
     *
     * @param array $simpleSku
     * @param array $gwData
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function giftWrappingPerOrderAllowed($simpleSku, $gwData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('order_gift_wrapping_design' => $gwData['gift_wrapping_design']))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertTrue($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'), 'Cannot find the block');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-984: Gift Wrapping for entire Order is not allowed (wrapping-no; messages-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "Yes"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "No"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Look at Gift Options block of create Order page</p>
     * <p>Expected result:</p>
     * <p>In "Gift Options" block of Order page dropdown "Gift Wrapping Design" should be absent</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftWrappingPerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-966: Gift Options for entire Order is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "No"</p>
     * <p>3. In system configuration setting "Allow Printed Card" is set to "No"</p>
     * <p>4. In system configuration setting "Allow Gift Receipt" is set to "No"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Look at Gift Options block of create Order page</p>
     * <p>Expected result:</p>
     * <p>"Gift Options" block should be absent on create Order Page</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftOptionsPerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_and_wrapping_all_disable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-933: Gift Message for Individual Item is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages for Order Items" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: Simple Product)</p>
     * <p>4. Select any shipping method (for example: Free Shipping), any payment method</p>
     * <p>(for example: Check/Money order), shipping/billing addresses</p>
     * <p>5. Click "Gift Options" link below product in a Items Ordered grid</p>
     * <p>6. In the opened AJAX-popup fill the fields related to Gift Message for Order Item (To/From/Message),</p>
     * <p> then click "OK" button;</p>
     * <p>7. When popup is closed, click "Gift Options" link for this product again, look at Gift Message fields;</p>
     * <p>8. Click "Cancel" in AJAX-popup;</p>
     * <p>9. Click "Submit Order" button</p>
     * <p>10. When Order is placed, click at Gift Options link below product name in Items Ordered grid</p>
     * <p> of View order page</p>
     * <p>Expected result:</p>
     * <p>7. All data entered in fields(To/From/Message) related to Gift Message in the previous step should be stored</p>
     * <p>10. AJAX-popup appears. Data in fields related to Gift Message for Order Item(To/From/Message) should be</p>
     * <p> present (the same, as entered when Order was placed)</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessageForIndividualItemAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_individual',
                      array('sku_product' => $simpleSku))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        $this->orderHelper()->verifyGiftOptions($orderData);
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-987: Gift Message for Individual Items is not allowed(message=no, wrapping=yes)</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages for Order Items" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping for Order Items" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: Simple Product)</p>
     * <p>4. Click "Gift Options" link below product in a Items Ordered grid</p>
     * <p>5. Look at blocks in the opened AJAX-popup;</p>
     * <p>Expected result:</p>
     * <p>There should be no Gift Message for Individual Item prompt fields (To/From/Message) in AJAX-popup</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     * @group skip_due_to_bug
     * @TODO: Blocked by https://jira.magento.com/browse/MAGE-5448'
     */
    public function giftMessageForIndividualItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        $this->clickControl('link', 'gift_options', false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_gift_message_block'));
    }

    /**
     * <p>TL-MAGE-938: Gift Wrapping for Individual Item is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping for Order Items" is set to "Yes"</p>
     * <p>2. At least one Gift Wrapping is created and enabled (for example, with Price $10 and image specified)</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: Simple Product)</p>
     * <p>4. Select any shipping method (for example: Free Shipping), any payment method</p>
     * <p>(for example: Check/Money order), shipping/billing addresses</p>
     * <p>5. Click "Gift Options" link below product in a Items Ordered grid</p>
     * <p>6. In the opened AJAX-popup select Gift Wrapping (from Preconditions) in "Gift Wrapping Design"</p>
     * <p>  dropdown</p>
     * <p>7. When popup is closed, click "Gift Options" link for this product again, look at Gift Message fields;</p>
     * <p>8. Click "Cancel" in AJAX-popup;</p>
     * <p>9. Click "Submit Order" button</p>
     * <p>10. When Order is placed, click at Gift Options link below product name in Items Ordered grid</p>
     * <p> of View order page</p>
     * <p>Expected result:</p>
     * <p>6. Image and price for selected Gift Wrapping should appear below dropdown ("Price: $10.00" in this</p>
     * <p> example)</p>
     * <p>7. AJAX-popup is closed, row with Gift Wrapping for Individual Item should appear in Product Grid with</p>
     * <p> price ($10 in this example) below desired product, also row should appear in Order Totals block</p>
     * <p> ("Gift Wrapping for Items: $10.00" in this example)</p>
     * <p>8. Selected in the previous step  Gift Wrapping should remain selected in "Gift Wrapping Design" dropdown;</p>
     * <p> present (the same, as entered when Order was placed)</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     * @group skip_due_to_bug
     * @TODO: Blocked by https://jira.magento.com/browse/MAGE-5448
     */
    public function giftWrappingForIndividualItemAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('SalesOrder', 'gift_wrapping_for_item',
            array('sku_product' => $simpleSku, 'product_gift_wrapping_design' => $gwDataMain['gift_wrapping_design']));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'gift_messages' => $giftWrappingData));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData);
        $this->orderHelper()->submitOrder();
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-989: Gift Options for Individual Items is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping on Order Items" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Messages on Order Items" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product)</p>
     * <p>4. Click "Gift Options" link below the product in grid</p>
     * <p>5. Look at blocks in AJAX-popup, that appears</p>
     * <p>Expected result:</p>
     * <p>There should be no "Gift Wrapping Design" dropdown in AJAX-popup</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftWrappingPerItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        $this->clickControl('link', 'gift_options', false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-985: Gift Options for Individual Items is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Items" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Items" is set to "No"</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Look at newly added product row in grid;</p>
     * <p>Expected result:</p>
     * <p>"Gift Options" block should be absent on create Order Page;</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftOptionsPerItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'gift_options'), 'Link is present');
    }

    /**
     * @TODO Move from MAUTOSEL-259 branch to here
     */

    /**
     * <p>TL-MAGE-914: Edit order case</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration Gift Wrapping(for entire Order and Items), Gift Messages(for entire Order</p>
     * <p> and Items), Printed Cards, Gift Receipt is allowed;</p>
     * <p>2. At least one Gift Wrapping is created and enabled(with price $10, for example);</p>
     * <p>3. Printed Card price is specified in configuration($1 for this example);</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list;</p>
     * <p>4. Fill in all necessary fields (Shipping/billing addresses, Shipping/Payment methods, etc);</p>
     * <p>5. In Gift Options block - select Gift Wrapping for Entire Order using "Gift Wrapping Design" dropdown;</p>
     * <p>6. Check "Add Printed Card" checkbox in Gift Options block;</p>
     * <p>7. Check "Send Gift Receipt" checkbox in Gift Options block;</p>
     * <p>8. Fill the fields corresponding to Gift Message for Entire Order in Gift Options block;</p>
     * <p>9. Click "Gift Options" link near product in Items Ordered list;</p>
     * <p>10. In appearing AJAX-popup window select Gift Wrapping for Order Item, fulfill fields, corresponding to</p>
     * <p> Gift Message for Individual Item, click "OK" button in popup;</p>
     * <p>11. Click "Submit Order" button;</p>
     * <p>12. When Order is placed, click "Edit" link on Order page;</p>
     * <p>13. Look at Gift Options block;</p>
     * <p>14. Click "Gift Options" link near product in Items Ordered Grid;</p>
     * <p>15. In AJAX-popup look at "Gift Wrapping Design" dropdown, Gift Message for Individual Item;</p>
     * <p>Expected result:</p>
     * <p>Gift Wrapping for Entire Order which was selected at time when Order is placed should remain in</p>
     * <p>"Gift Wrapping Design" dropdown ;</p>
     * <p>"Add Printed Card" checkbox in Gift Options block should be checked</p>
     * <p>"Send Gift Receipt" checkbox in Gift Options block should be checked;</p>
     * <p>Gift Message for entire Order fields should be filled with data, entered when Order was placed;</p>
     * <p>Gift Wrapping for Individual Item which was selected at time when Order is placed should remain in</p>
     * <p>"Gift Wrapping Design" dropdown;</p>
     * <p>Gift Message for Individual Item fields should be filled with data, entered at time, when Order was</p>
     * <p>placed;</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function editOrderGiftWrappingAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $simpleSku, 'giftWrappingDesign' => $gwDataMain['gift_wrapping_design']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_default_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-923: ReOrder case</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration Gift Wrapping(for entire Order and Items), Gift Messages(for entire Order</p>
     * <p> and Items), Printed Cards, Gift Receipt is allowed;</p>
     * <p>2. At least one Gift Wrapping is created and enabled(with price $10, for example);</p>
     * <p>3. Printed Card price is specified in configuration($1 for this example);</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list;</p>
     * <p>4. Fill in all necessary fields (Shipping/billing addresses, Shipping/Payment methods, etc);</p>
     * <p>5. In Gift Options block - select Gift Wrapping for Entire Order using "Gift Wrapping Design" dropdown;</p>
     * <p>6. Check "Add Printed Card" checkbox in Gift Options block;</p>
     * <p>7. Check "Send Gift Receipt" checkbox in Gift Options block;</p>
     * <p>8. Fill the fields corresponding to Gift Message for Entire Order in Gift Options block;</p>
     * <p>9. Click "Gift Options" link near product in Items Ordered list;</p>
     * <p>10. In appearing AJAX-popup window select Gift Wrapping for Order Item, fulfill fields, corresponding to</p>
     * <p> Gift Message for Individual Item, click "OK" button in popup;</p>
     * <p>11. Click "Submit Order" button;</p>
     * <p>12. When Order is placed, click "Reorder" button on Order page;</p>
     * <p>13. Look at Gift Options block;</p>
     * <p>14. Click "Gift Options" link near product inItems Ordered Grid;</p>
     * <p>15. In AJAX-popup look at "Gift Wrapping Design" dropdown, Gift Message for Individual Item;</p>
     * <p>Expected result:</p>
     * <p>Gift Wrapping for Entire Order should not be selected in "Gift Wrapping Design" dropdown; </p>
     * <p>"Add Printed Card" checkbox in Gift Options block should not be checked</p>
     * <p>"Send Gift Receipt" checkbox in Gift Options block should not be checked</p>
     * <p>Gift Message for entire Order field should be blank</p>
     * <p>Gift Wrapping for Individual Item should not be selected in "Gift Wrapping Design" dropdown</p>
     * <p>Gift Message for Individual Item field should be blank</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function reorderOrderGiftWrappingAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $simpleSku, 'giftWrappingDesign' => $gwDataMain['gift_wrapping_design']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_default_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        //Verification
        $giftOptions =
            $this->loadDataSet('SalesOrder', 'reorder_empty_gift_options', null, array('product1' => $simpleSku));
        $this->orderHelper()->verifyGiftOptions(array('gift_messages' => $giftOptions));
    }

    /**
     * <p>TL-MAGE-929: Gift Receipt is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Receipt" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Select any shipping method(for example: Free Shipping), any payment method</p>
     * <p> (for example: Check/Money order), shipping/billing addresses;</p>
     * <p>5. Look at Gift Options block of create Order page;</p>
     * <p>6. Check "Send Gift Receipt" checkbox in Gift Options block;</p>
     * <p>7. Click "Submit Order" button. When Order is placed, look at Gift Options block;</p>
     * <p>Expected result:</p>
     * <p>5. Checkbox "Send Gift Receipt" should be present in Gift Options block of Order creation page;</p>
     * <p>7. Order is placed, Order View page opened. Gift options block should contain "Send Gift Receipt"</p>
     * <p> checkbox, and it should be checked</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderGiftReceiptAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid'),
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('send_gift_receipt' => 'Yes'))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        // Verification
        $this->assertTrue($this->controlIsPresent('checkbox', 'send_gift_receipt'), 'Checkbox is absent or unchecked');
    }

    /**
     * <p>TL-MAGE-953: Printed Card is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Printed Card" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Select any shipping method(for example: Free Shipping), any payment method</p>
     * <p> (for example: Check/Money order), shipping/billing addresses;</p>
     * <p>5. Look at Gift Options block of create Order page;</p>
     * <p>6. Check "Add Printed Card" checkbox;</p>
     * <p>7. Click "Submit Order" button. When Order is placed, look at Gift Options block;</p>
     * <p>Expected result:</p>
     * <p>5. Checkbox "Add Printed Card" should be present in Gift Options block of Order creation page;</p>
     * <p>6. Printed card price must appear below the checkbox (for example: Price: $1.00) and it Order Totals block</p>
     * <p> ("Printed Card: $1.00" for example");</p>
     * <p>7. Order is placed, Order View page opened. Gift options block should contain "Add Printed Card"</p>
     * <p> checkbox, and it should be checked. Printed Сard price should be included in Order Totals block</p>
     * <p> ("Printed Card: $1.00" for example)</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderPrintedCardAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid'),
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('add_printed_card' => 'Yes'))));
        //Configuration
        $this->navigate('system_configuration');
        $printedCardOptions = $this->loadDataSet('GiftMessage', 'gift_printed_card_enable');
        $this->systemConfigurationHelper()->configure($printedCardOptions);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertTrue($this->controlIsPresent('checkbox', 'add_printed_card'), 'Printed Card is not added');
        $this->orderHelper()->verifyPageelement('printed_card_price',
            '$' . $printedCardOptions['tab_1']['configuration']['default_price_for_printed_card']);
        $this->orderHelper()->verifyPageelement('total_printed_card_price',
            '$' . $printedCardOptions['tab_1']['configuration']['default_price_for_printed_card']);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>TL-MAGE-990: Printed Card is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Printed Card" is set to "No"</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Look at Gift Options block of create Order page;</p>
     * <p>Expected result:</p>
     * <p>5. Checkbox "Add Printed Card" should be absent in Gift Options block of Order creation page;</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderPrintedCardNotAllowed($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_disable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        //Verification
        //If product is not added 'send_gift_receipt' checkbox will be absent
        $this->addParameter('sku', $simpleSku);
        $this->assertTrue($this->controlIsPresent('field', 'product_qty'), 'Product is not added');
        $this->assertFalse($this->controlIsPresent('checkbox', 'add_printed_card'), 'Checkbox is present');
    }

    /**
     * <p>TL-MAGE-991: Gift Receipt is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Receipt" is set to "No"</p>
     * <p>Steps:</p>
     * <p>1. Log in to Backend;</p>
     * <p>2. Start creating new Order, select customer and store;</p>
     * <p>3. Add any product to Items Ordered list (for example: simple product);</p>
     * <p>4. Look at Gift Options block of create Order page;</p>
     * <p>Expected result:</p>
     * <p>5. Checkbox "Send Gift Receipt" should be absent in Gift Options block of Order creation page;</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderGiftReceiptDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_disable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        //Verification
        //If product is not added 'send_gift_receipt' checkbox will be absent
        $this->addParameter('sku', $simpleSku);
        $this->assertTrue($this->controlIsPresent('field', 'product_qty'), 'Product is not added');
        $this->assertFalse($this->controlIsPresent('checkbox', 'send_gift_receipt'), 'Checkbox is present');
    }
}
