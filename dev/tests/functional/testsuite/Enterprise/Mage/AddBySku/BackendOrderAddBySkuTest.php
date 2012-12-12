<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AddBySku
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for creating order in backend using Add By SKU functionality
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_AddBySku_BackendOrderAddBySkuTest extends Mage_Selenium_TestCase
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
        $this->addBySkuHelper()->verifyProductAbsentInGrid($product[0],
                                                           array('sku_product_required', 'sku_product_added'));
        $this->addBySkuHelper()->verifyProductAbsentInGrid($product[1],
                                                           array('sku_product_required', 'sku_product_added'));
    }

    /**
     *<p>Adding/Removing each attention product separately</p>
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
        $sku = array($productDataNonExist[0]['sku'], $productDataNonExist[1]['sku'], $productDataNonExist[2]['sku']);

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
        $this->addBySkuHelper()->verifyProductPresentInGrid($sku, 'sku_product_required');
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
        $this->fillFieldset(array('sku' => $data['simple_sku']['sku'], 'qty_sku' => $qtySku),
                            'order_items_ordered_sku');
        $this->clickButton('submit_sku_form', true);
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku']['sku'], 'sku_product_required');
        $this->assertTrue($this->controlIsPresent('pageelement', 'qty_invalid_qty'));
        $this->assertTrue($this->controlIsPresent('field', 'required_qty_enabled'));
        $this->addBySkuHelper()->getRowCount('field', 'required_qty', 'lastRow');
        $this->assertTrue($this->controlIsPresent('pageelement', 'required_product_message'));
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku_full']['product_name'],
                                                            'sku_product_required');
        $this->addBySkuHelper()->verifyProductPresentInGrid($data['simple_sku_full']['product_price'],
                                                            'sku_product_required');
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
