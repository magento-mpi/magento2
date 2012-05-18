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
class Enterprise_Mage_Order_GiftWrapping_GiftWrappingTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_options_disable_all'));
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
     * @return array $gwData
     *
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
     * @TODO Move from MAUTOSEL-259 branch to here
     */
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
     * @depends createSimpleProduct
     * @param array $simpleSku
     *
     * @test
     */
    public function createOrderPrintedCardNotAllowed($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_printed_card_disable');
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
     * @depends createSimpleProduct
     * @param array $simpleSku
     *
     * @test
     */
    public function createOrderGiftReceiptDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_receipt_disable');
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
