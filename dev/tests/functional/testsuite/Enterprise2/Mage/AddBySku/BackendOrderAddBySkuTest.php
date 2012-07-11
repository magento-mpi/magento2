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
 * Tests for creating order in backend using Add By SKU functionality
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_AddBySku_BackendOrderAddBySkuTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @return array
     * @test
     */
    public function preconditionForTests()
    {
        //Data
        $productDataSimple = $this->loadDataSet('Product', 'simple_product_visible');
        $productDataVirtual = $this->loadDataSet('Product', 'virtual_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDataSimple);
        $this->productHelper()->createProduct($productDataVirtual, 'virtual');

        return array('simple_sku' => array('sku' => $productDataSimple['general_sku'], 'qty_sku' => 5),
                     'virtual_sku'=> array('sku' => $productDataVirtual['general_sku'], 'qty_sku' => 1),
                     'simple_sku_full' => array('product_name'=>$productDataSimple['general_name'],
                                                'product_price'=>$productDataSimple['prices_price']),
                     'virtual_sku_full' => array('product_name'=>$productDataVirtual['general_name'],
                                                'product_price'=>$productDataVirtual['prices_price']));
    }

    /**
     * <p>Displaying Add to Order by SKU</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Go to Sales>Orders;</p>
     * <p>3. Start to create new Order.;</p>
     * <p>4. Click the "Add Products By SKU" button.;</p>
     * <p>Expected Results:
     * <p>1. Add to Order by SKU section should be displayed;<p/>
     * <p>2. The "Add to Order by SKU" field contains the following UI elements:SKU and Qty text fields; a button for adding more fields for SKUs and QTYs.;<p/>
     * <p>3. The Add to Order by SKU field contains "File" label, Browse button for loading a spreadsheet and Reset button for removing selected file.;<p/>
     * <p>4. Two notes: "Allowed file type: csv. and File must contain two columns, with "sku" and "qty" in the header row" are displayed under "File" field.;<p/>
     *
     * @test
     * @TestlinkId TL-MAGE-4183
     */
    public function addBySkuCheckingControls()
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->assertTrue($this->buttonIsPresent('add_products_by_sku'),
                                                 'There is no "Add Products By SKU" button on the page');
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->assertTrue($this->buttonIsPresent('add_by_sku_add'),
                                                 'There is no button for adding rows for SKU on the page');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 1);
        $this->assertTrue($this->buttonIsPresent('add_by_sku_del'),
                                                 'There is no button for deleting rows for SKU on the page');
        $this->clickButton('add_by_sku_del', false);
        $this->assertTrue($this->controlIsPresent('field', 'sku_upload'),
                                                  'There is no File field for adding products per *.CSV');
        $this->assertTrue($this->controlIsPresent('pageelement', 'note_csv'), 'There is no note about *.csv file');
        $this->assertTrue($this->buttonIsPresent('reset_upload'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('submit_sku_form'), 'There is no "Add To Order" button on the page');
    }

    /**
     * <p>Adding to Order by SKU after entering SKU and QTY manually</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Go to Sales>Orders;</p>
     * <p>3. Start to create new Order;</p>
     * <p>4. Click the "Add Products By SKU" button;</p>
     * <p>5. Enter valid SKU in SKU field;</p>
     * <p>6. Enter valid value in Qty field;</p>
     * <p>7. Click "Add to Order" button
     * <p>Expected result:
     * <p>Simple product is added to the Order Items.
     *
     * @param array $data
     *
     * @depends preconditionForTests
     * @test
     * @TestlinkId TL-MAGE-4197
     */
    public function addBySkuProductValid($data)
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        $productInfo = array($data['simple_sku']['sku'],
                             $data['simple_sku_full']['product_name'],
                             $data['simple_sku_full']['product_price']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset($data['simple_sku'], 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($productInfo, 'sku_product_added');
    }

    /**
     *<p>Adding to Order by SKU after entering values in multiple fields</p>
     *<p>1. Login to Backend</p>
     *<p>2. Go to Sales -> Orders.</p>
     *<p>3. Start to create new Order.</p>
     *<p>4. Click the "Add Products by SKU" button.</p>
     *<p>5. Click "Add Row" button several times.</p>
     *<p>6. Enter both valid and invalid SKUs and QTYs values.</p>
     *<p>7. Click the "Add to Order" button.</p>
     *<p>Expected results:</p>
     *<p>Products with valid SKU are added to Items Ordered grid.</p>
     *<p>Product with invalid SKU are added to the "product(s) require attention" grid.</p>
     *
     * @param array $data
     *
     * @depends preconditionForTests
     * @test
     * @TestlinkId TL-MAGE-4198
     */
    public function addBySkuMultipleProducts($data)
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        $productDataNonExist = $this->loadDataSet('SkuProducts', 'non_exist_product_back');
        $productExist = array($data['simple_sku']['sku'], $data['virtual_sku']['sku']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset($data['simple_sku'], 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 1);
        $this->fillFieldset($data['virtual_sku'], 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 2);
        $this->fillFieldset($productDataNonExist, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($productExist, 'sku_product_added');
        $this->addBySkuHelper()->verifyProductPresentInGrid($productDataNonExist['sku'], 'sku_product_required');
    }

    /**
     *<p>Adding/Removing all attention products</p>
     *<p>1. Login to Backend</p>
     *<p>2. Go to Sales -> Orders.</p>
     *<p>3. Start to create new Order.</p>
     *<p>4. Click the "Add Products by SKU" button.</p>
     *<p>5. Click the "Add Row" button several time.</p>
     *<p>6. In fields "SKU" enter non-existing SKU of products and click button "Add to Order"</p>
     *<p>7. Click "Remove All" button</p>
     *<p>Expected results:</p>
     *<p>After Step 6. All product should be added to "require attention" grid</p>
     *<p>After Step 7. All products are deleted. Failed grid is disappeared.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4237
     */
    public function MultipleInvalidSkuDeleteAll()
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        $productDataNonExist = $this->loadDataSet('SkuProducts', 'non_exist_product_back');
        $productDataNonExist1 = $this->loadDataSet('SkuProducts', 'non_exist_product_back');
        $product = array ($productDataNonExist['sku'], $productDataNonExist1['sku']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset($productDataNonExist, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 1);
        $this->fillFieldset($productDataNonExist1, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($product, 'sku_product_required');
        $this->clickButton('remove_all_invalid', false);
        $this->pleaseWait();
        $this->assertFalse($this->controlIsPresent('pageelement', 'sku_required_grid'),
                                                   'Required attention grid is present on the page');
        $this->addBySkuHelper()->verifyProductAbsentInGrid($product, array('sku_product_required', 'sku_product_added'));
    }

    /**
     *<p>Adding/Removing each attention product separately</p>
     *<p>1. Login to Backend</p>
     *<p>2. Go to Sales -> Orders.</p>
     *<p>3. Start to create new Order.</p>
     *<p>4. Click the "Add Products by SKU" button.</p>
     *<p>5. Click on "Add row" button three time (e.g.)</p>
     *<p>6. In "SKU" fields enter non-existing SKU of products.</p>
     *<p>7. Click the "Add to Order" button.</p>
     *<p>8. Remove one product.</p>
     *<p>9. Take away all the products one by one.</p>
     *<p>Expected results:</p>
     *<p>After Step 7: Three products should be added to "require attention" grid.</p>
     *<p>After Step 8: One product should be deleted, two products should be displayed in grid.</p>
     *<p>After Step 9: All products should be deleted one by one. Grid without product should be disappear.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4239
     */
    public function MultipleInvalidSkuDeleteSeparately()
    {
        // Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        $productDataNonExist = array(('0') => $this->loadDataSet('SkuProducts', 'non_exist_product_back'),
                                     ('1') => $this->loadDataSet('SkuProducts', 'non_exist_product_back'),
                                     ('2') => $this->loadDataSet('SkuProducts', 'non_exist_product_back'));
        $a = array($productDataNonExist[0]['sku'], $productDataNonExist[1]['sku'], $productDataNonExist[2]['sku']);

        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset($productDataNonExist[0], 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 1);
        $this->fillFieldset($productDataNonExist[1], 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 2);
        $this->fillFieldset($productDataNonExist[2], 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($a, 'sku_product_required');
        $this->addParameter('row', 3);
        $this->clickButton('remove_one_invalid', false);
        $this->pleaseWait();
        $this->addBySkuHelper()->verifyProductAbsentInGrid($productDataNonExist[2]['sku'], array('sku_product_required',
                                                                                                 'sku_product_added'));
        $this->addBySkuHelper()->verifyProductPresentInGrid($productDataNonExist[0]['sku'], 'sku_product_required');
        $this->addBySkuHelper()->verifyProductPresentInGrid($productDataNonExist[1]['sku'], 'sku_product_required');
        $this->addParameter('row', 2);
        $this->clickButton('remove_one_invalid', false);
        $this->pleaseWait();
        $this->AddBySkuHelper()->verifyProductAbsentInGrid($productDataNonExist[1]['sku'], array('sku_product_required',
                                                                                                 'sku_product_added'));
        $this->addParameter('row', 1);
        $this->clickButton('remove_one_invalid', false);
        $this->pleaseWait();
        $this->addBySkuHelper()->verifyProductAbsentInGrid($productDataNonExist[0]['sku'], array('sku_product_required',
                                                                                                 'sku_product_added'));
        $this->assertFalse($this->controlIsPresent('pageelement', 'sku_required_grid'),
                                                   'Required attention grid is present on the page');
    }

    /**
     * <p>QTY field validation</p>
     * <p>Steps:</p>
     * <p>1. 1. Log in to Backend</p>
     * <p>2. Go to Sales -> Order.</p>
     * <p>3. Start to create new Order for new Customer</p>
     * <p>4. Select store view.</p>
     * <p>5. Click the "Add Products by SKU" button.</p>
     * <p>6. Enter valid value in SKU field and enter non numeric value in QTY field.</p>
     * <p>7. Add new row.</p>
     * <p>8. Enter valid value in SKU field and enter negative value in QTY field.</p>
     * <p>9. Add new row.</p>
     * <p>10. Enter valid value in SKU field and enter 0 value in Qty field.</p>
     * <p>11. Add new row.</p>
     * <p>12. Enter a valid value in SKU field and enter less than 0.0001 value in Qty field.</p>
     * <p>13. Add new row.</p>
     * <p>14. Enter a valid value in SKU field and enter greater than 99999999.9999 value in Qty field.</p>
     * <p>15. Click the "Add to Order" button.</p>
     * <p>Expected results:</p>
     * <p>After Step 5. SKU and Qty fields are present</p>
     * <p>After Step 15. SKU and Qty fields validation is performed without errors.</p>
     * <p>System displays message "1 product requires your attention."</p>
     * <p>Product is added to the Products Requiring Attention grid.</p>
     * <p>The Product Name column contains product name, product SKU and text message "Please enter a valid number in the "Qty" field."</p>
     * <p>The Price column displays product base price.</p>
     * <p>The value in the QTY column can be edited.</p>
     *
     * @param array $data
     * @param string $qtySku
     *
     * @dataProvider qtySkuDataProvider
     * @depends preconditionForTests
     * @test
     * @TestlinkId TL-MAGE-5241
     */
    public function addBySkuQtyValidation ($qtySku, $data)
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset(array('sku' => $data['simple_sku']['sku'], 'qty_sku' => $qtySku), 'order_items_ordered_sku');
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku']['sku'], 'sku_product_required');
        $this->assertTrue($this->controlIsPresent('pageelement', 'qty_invalid_qty'));
        $this->assertTrue($this->controlIsPresent('field', 'required_qty_enabled'));
        $this->addBySkuHelper()->getRowCount('field', 'required_qty', 'lastRow');
        $this->assertTrue($this->controlIsPresent('pageelement', 'required_product_message'));
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku_full']['product_name'], 'sku_product_required');
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku_full']['product_price'], 'sku_product_required');
    }


    public function qtySkuDataProvider()
    {
        return array(
            array('non-num'),
            array('-5'),
            array('0'),
            array('0.00001'),
            array('999999999.9999'),
        );
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     * <p>Steps:</p>
     * <p>1. 1. Log in to Backend</p>
     * <p>2. Go to Sales -> Order.</p>
     * <p>3. Start to create new Order for new Customer</p>
     * <p>4. Select store view.</p>
     * <p>5. Click the "Add Products by SKU" button.</p>
     * <p>6. Leave Qty and SKU fields empty.</p>
     * <p>7. Click the "Add to Order" button.</p>
     * <p>8. Click the "Add Product by SKU" button.</p>
     * <p>9. Leave SKU field empty and enter a valid value in Qty field.</p>
     * <p>10. Add new row.</p>
     * <p>11. Enter a valid SKU and Qty.</p>
     * <p>12. Click the "Add to Order" button.</p>
     * <p>13. Click the "Add Product by SKU" button.</p>
     * <p>14. Enter a valid value in SKU field and leave Qty field empty.</p>
     * <p>15. Click the "Add to Order" button.</p>
     * <p>Expected results:</p>
     * <p>After Step 7. Add to Order by SKU fieldset has been disappeared. Any message does not appear.</p>
     * <p>After Step 12. Product with valid SKU is added to ItemsOrdered grid. The row, for which SKU value is empty, should be ignored.</p>
     * <p>After Step 15. Product is added to the Products Requiring Attention grid with text message "Please enter a valid number in the "Qty" field."</p>
     *
     * @depends preconditionForTests
     * @param array $data
     *
     * @test
     * @TestlinkId TL-MAGE-5242
     */
    public function addBySkuEmptyValidation ($data)
    {
        //Data
        $orderData = $this->loadDataSet('Order', 'new_order_sku');
        $productInfo = array($data['simple_sku']['sku'],
                             $data['simple_sku_full']['product_name'],
                             $data['simple_sku_full']['product_price']);
        $productInfoVirtual = array($data['virtual_sku']['sku'],
                                    $data['virtual_sku_full']['product_name']);

        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset(array('sku' => null, 'qty_sku' => null), 'order_items_ordered_sku');
        $this->clickButton('submit_sku_form', false);
        $this->assertFalse($this->controlIsPresent('pageelement', 'add_by_sku_fieldset'));
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset(array('sku' => null, 'qty_sku' => '5'), 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addParameter('row', 1);
        $this->fillFieldset($data['simple_sku'], 'order_items_ordered_sku');
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($productInfo, 'sku_product_added');
        $this->assertFalse($this->controlIsPresent('pageelement', 'add_by_sku_fieldset'));
        $this->clickButton('add_products_by_sku', false);
        $this->addParameter('row', 0);
        $this->fillFieldset(array('sku' => $data['virtual_sku']['sku'], 'qty_sku' => null), 'order_items_ordered_sku');
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($productInfoVirtual, 'sku_product_required');
        $this->addBySkuHelper()->getRowCount('field', 'required_qty', 'lastRow');
        $this->assertTrue($this->controlIsPresent('pageelement', 'required_product_message'));
    }
}
