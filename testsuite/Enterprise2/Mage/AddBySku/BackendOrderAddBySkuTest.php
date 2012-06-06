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
     * @group preConditions
     * @return type
     * @return array
     * @test
     */
    public function preconditionForTests()
    {
        //Data
        $productDataSimple = $this->loadDataSet('AddBySku', 'simple_product_visible_sku');
        $productDataVirtual = $this->loadDataSet('AddBySku', 'virtual_product_visible_sku');
        //Steps
        $this->AddBySkuHelper()->createSimpleProduct('simple_product_visible_sku');
        $this->AddBySkuHelper()->createVirtualProduct('virtual_product_visible_sku');
        return array ('simple_sku' => array ('sku' => $productDataSimple['general_sku']),
                      'virtual_sku'=> array ('sku' => $productDataVirtual['general_sku']));
    
    }
    /**
     * <p>Displaying Add to Order by SKU</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend;</p>
     * <p>2. Go to Sales>Orders;</p>
     * <p>3. Start to create new Order.;</p>
     * <p>4. Click the "Add Products By SKU" button.;</p>
     * <p>5. Verify that:
     * <p>1. Add to Order by SKU section should be displayed;<p/>
     * <p>    2. The "Add to Order by SKU" field contains the following UI elements:SKU and Qty text fields; a button for adding more fields for SKUs and QTYs.;<p/>     
     * <p>    3. The Add to Order by SKU field contains "File" label, Browse button for loading a spreadsheet and Reset button for removing selected file.;<p/>
     * <p>    4. Two notes: "Allowed file type: csv. and File must contain two columns, with "sku" and "qty" in the header row" are displayed under "File" field.;<p/>
     * 
     * @test
     * @TestlinkId TL-MAGE-4183
     */
    public function addBySkuCheckingControls()
    {
        //Data
        $orderData = $this->loadDataSet('AddBySku', 'new_order_sku');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);       
        //Verifying        
        $this->assertTrue($this->buttonIsPresent('add_products_by_sku'),
                                                 'There is no "Add Products By SKU" button on the page');
        $this->clickButton('add_products_by_sku', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');    
        $this->assertTrue($this->buttonIsPresent('add_by_sku_add'),
                                                 'There is no button for adding rows for SKU on the page');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('1'); 
        $this->assertTrue($this->buttonIsPresent('add_by_sku_del'),
                                                 'There is no button for deleting rows for SKU on the page');
        $this->clickButton('add_by_sku_del', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');          
        $this->assertTrue($this->controlIsPresent('field', 'sku_upload'),
                                                  'There is no File field for adding products per *.CSV');
        $this->assertTrue($this->controlIsPresent('pageelement', 'note_csv'),
                                                  'There is no note about *.csv file');
        $this->assertTrue($this->buttonIsPresent('reset_upload'),
                                                 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('submit_sku_form'), 
                                                 'There is no "Add To Order" button on the page');  
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
     * @depends preconditionForTests
     * @test
     * @TestlinkId TL-MAGE-4197
     */
    public function addBySkuProductValid()
    {
        //Data
        $orderData = $this->loadDataSet('AddBySku', 'new_order_sku');
        $productData = $this->loadDataSet('AddBySku', 'simple_product_visible_sku');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');
        $skuproduct = array('sku'=>$productData['general_sku'],'qty_sku'=>'1', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductAdded($productData['general_sku']);
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
     * @depends preconditionForTests 
     * @test
     * @TestlinkId TL-MAGE-4198
     */
    
    public function addBySkuMultipleProducts()
    {
        //Data
        $orderData = $this->loadDataSet('AddBySku', 'new_order_sku');
        $productData = $this->loadDataSet('AddBySku', 'simple_product_visible_sku');
        $productDataExist = $this->loadDataSet('AddBySku', 'virtual_product_visible_sku');
        $productDataNonExist = $this->loadDataSet('AddBySku', 'sku_product_non_exist_0');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');
        $skuproduct = array('sku'=>$productData['general_sku'],'qty_sku'=>'1', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('1');
        $skuproductvalid = array('sku'=>$productDataExist['general_sku'], 'qty_sku'=>'5', 'sku_upload'=>null);
        $this->fillFieldset($skuproductvalid, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('2');
        $skuproductinvalid = array('sku'=>$productDataNonExist['general_sku'], 'qty_sku'=>'2', 'sku_upload'=>null);
        $this->fillFieldset($skuproductinvalid, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductAdded($productData['general_sku']);
        $this->addBySkuHelper()->verifyProductAdded($productDataExist['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist['general_sku']);
        
    }
    /**
     *<p>Adding/Remowing all attention products</p>
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
        $orderData = $this->loadDataSet('AddBySku', 'new_order_sku');
        $productDataNonExist = $this->loadDataSet('AddBySku', 'sku_product_non_exist_0');
        $productDataNonExist1 = $this->loadDataSet('AddBySku', 'sku_product_non_exist_1');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->clickButton('add_products_by_sku', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');
        $skuproduct1 = array('sku'=>$productDataNonExist['general_sku'],'qty_sku'=>'1', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct1, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('1');
        $skuproduct2 = array('sku'=>$productDataNonExist1['general_sku'], 'qty_sku'=>'5', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct2, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist1['general_sku']);
        $this->saveForm('remove_all_invalid', false);
        $this->assertFalse($this->ControlIsPresent('pageelement','sku_required_grid'),
                                                   'Required attention grid is present on the page');
        $this->addBySkuHelper()->verifyProductAbsentOnPage ($productDataNonExist['general_sku']);
        $this->addBySkuHelper()->verifyProductAbsentOnPage ($productDataNonExist1['general_sku']);
    }
    
    /**
     *<p>Adding/Removing each attention product separately</p>
     *<p>1. Login to Backend</p>
     *<p>2. Go to Sales -> Orders.</p>
     *<p>3. Start to create new Order.</p>
     *<p>4. Click the "Add Products by SKU" button.</p>
     *<p>5. Click on "Add row" button three time (e.g.)</p>
     *<p>6. In "SKU" fileds enter non-existing SKU of products.</p>
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
        //Data
        $orderData = $this->loadDataSet('AddBySku', 'new_order_sku');
        $productDataNonExist = $this->loadDataSet('AddBySku', 'sku_product_non_exist_0');
        $productDataNonExist1 = $this->loadDataSet('AddBySku', 'sku_product_non_exist_1');
        $productDataNonExist2 = $this->loadDataSet('AddBySku', 'sku_product_non_exist_2');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying        
        $this->clickButton('add_products_by_sku', false);
        $this->addBySkuHelper()->verifySkuQtyRow('0');
        $skuproduct1 = array('sku'=>$productDataNonExist['general_sku'],'qty_sku'=>'1', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct1, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('1');
        $skuproduct2 = array('sku'=>$productDataNonExist1['general_sku'], 'qty_sku'=>'5', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct2, 'order_items_ordered_sku');
        $this->clickButton('add_by_sku_add', false);
        $this->addBySkuHelper()->verifySkuQtyRow('2');
        $skuproduct2 = array('sku'=>$productDataNonExist2['general_sku'], 'qty_sku'=>'11', 'sku_upload'=>null);
        $this->fillFieldset($skuproduct2, 'order_items_ordered_sku');
        $this->buttonIsPresent('submit_sku_form', false);
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist1['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist2['general_sku']);
        $this->addBySkuHelper()->identifySkuDeleteButton ('3');
        $this->saveForm('remove_one_invalid');
        $this->addBySkuHelper()->verifyProductAbsentOnPage ($productDataNonExist2['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist['general_sku']);
        $this->addBySkuHelper()->verifyProductRequiredAttention($productDataNonExist1['general_sku']);
        $this->addBySkuHelper()->identifySkuDeleteButton ('2');
        $this->saveForm('remove_one_invalid');
        $this->AddBySkuHelper()->verifyProductAbsentOnPage ($productDataNonExist1['general_sku']);
        $this->AddBySkuHelper()->identifySkuDeleteButton ('1');
        $this->saveForm('remove_one_invalid');
        $this->addBySkuHelper()->verifyProductAbsentOnPage ($productDataNonExist['general_sku']);
        $this->assertFalse($this->ControlIsPresent('pageelement','sku_required_grid'),
                                                   'Required attention grid is present on the page');   
    }
    /**
     * @test
     */
    public function postConditionForTest()
    {
    $this->AddBySkuHelper()->postConditionDeleteProduct('simple_product_visible_sku');
    $this->AddBySkuHelper()->postConditionDeleteProduct('virtual_product_visible_sku');
    }
}
