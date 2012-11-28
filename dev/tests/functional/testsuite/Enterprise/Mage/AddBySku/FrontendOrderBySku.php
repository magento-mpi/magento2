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
 * General flow for testing Order by SKU functionality on the Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_AddBySku_FrontendOrderBySkuTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Enable Order by SKU functionality on Frontend</p>
     * <p>Enable Category permissions</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $configSku = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_all');
        $configCategory = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($configSku);
        $this->systemConfigurationHelper()->configure($configCategory);
    }

    /**
     * <p>Creating website</p>
     *
     * @return string
     * @test
     */
    public function createWebsite()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $websiteData['website_name'];
    }

    /**
     * <p>Creating category</p>
     *
     * @return string
     *
     * @test
     */
    public function createCategory()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $category = $this->loadDataSet('Category', 'sub_category_required_permissions_deny');
        $this->categoryHelper()->createCategory($category);

        return $category['parent_category'] . '/' . $category['name'];
    }

    /**
     * <p>Creating new attribute for configurable products and adding it to default attribute set</p>
     *
     * @return array
     *
     * @test
     */
    public function createAttribute()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['attribute_code'];
        $associatedAttributes =
            $this->loadDataSet('AttributeSet', 'associated_attributes', array('General' => $attrCode));
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * <p>Creating customer</p>
     *
     * @return array
     *
     * @test
     */
    public function createCustomer()
    {
        $this->loginAdminUser();
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create all products for testing SKU functionality</p>
     *
     * @param string $website
     * @param string $category
     * @param string $attrData
     *
     * @return array
     *
     * @test
     * @depends createWebsite
     * @depends createCategory
     * @depends createAttribute
     */
    public function preconditionsForTests($website, $category, $attrData)
    {
        $this->loginAdminUser();
        //Custom options
        $customOptionsRequired = $this->loadDataSet('Product', 'custom_options_dropdown');
        $customOptionsNotRequired = $this->loadDataSet('Product', 'custom_options_dropdown',
            array('custom_options_general_is_required' => 'No'));
        //Simple products
        $simpleProducts = array();
        $simpleProducts['simple'] = $this->loadDataSet('SkuProducts', 'simple_sku');
        $simpleProducts['simple']['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $simpleProducts['simpleWithBackorders'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('inventory_backorders_default' => 'No', 'inventory_backorders' => 'Allow Qty Below 0'));
        $simpleProducts['simpleWithBackorders']['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_2']['admin_option_name'];
        $simpleProducts['simpleDisabled'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('general_status' => 'Disabled'));
        $simpleProducts['simpleCategory'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('categories' => $category));
        $simpleProducts['simpleWebsite'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('websites' => $website));
        $simpleProducts['simpleOutOfStock'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('inventory_stock_availability' => 'Out of Stock'));
        $simpleProducts['simple_not_visible'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('general_visibility' => 'Not Visible Individually'));
        $simpleProducts['simpleNotVisibleCustom'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('general_visibility' => 'Not Visible Individually'));
        $simpleProducts['simpleNotVisibleCustom']['custom_options_data'][] = $customOptionsRequired;
        $simpleProducts['simpleNotRequiredCustom'] = $this->loadDataSet('SkuProducts', 'simple_sku');
        $simpleProducts['simpleNotRequiredCustom']['custom_options_data'][] = $customOptionsNotRequired;
        $simpleProducts['simpleRequiredCustom'] = $this->loadDataSet('SkuProducts', 'simple_sku');
        $simpleProducts['simpleRequiredCustom']['custom_options_data'][] = $customOptionsRequired;
        $simpleProducts['simple_min'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('inventory_min_allowed_qty_default' => 'No', 'inventory_min_allowed_qty' => '5'));
        $simpleProducts['simple_max'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('inventory_max_allowed_qty_default' => 'No', 'inventory_max_allowed_qty' => '5'));
        $simpleProducts['simple_increment'] = $this->loadDataSet('SkuProducts', 'simple_sku',
            array('inventory_enable_qty_increments_default' => 'No', 'inventory_enable_qty_increments' => 'Yes',
                  'inventory_qty_increments_default' => 'No', 'inventory_qty_increments' => '5'));
        $this->navigate('manage_products');
        foreach ($simpleProducts as $product) {
            $this->productHelper()->createProduct($product);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        //Downloadable product
        $download = $this->loadDataSet('Product', 'downloadable_product_visible');
        $download['downloadable_information_data']['downloadable_link_1'] =
            $this->loadDataSet('Product', 'downloadable_links');
        $download['downloadable_information_data']['downloadable_link_2'] =
            $this->loadDataSet('Product', 'downloadable_links');
        $this->productHelper()->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Configurable products
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('configurable_attribute_title' => $attrData['admin_title']),
            array('associated_1' => $simpleProducts['simple']['general_sku'],
                  'associated_2' => $simpleProducts['simpleWithBackorders']['general_sku']));
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Grouped products
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', null,
            array('associated_1' => $simpleProducts['simple']['general_sku'],
                  'associated_2' => $simpleProducts['simpleWithBackorders']));
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $groupedVisibleIndividual = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', null,
            array('associated_1' => $simpleProducts['simple']['general_sku'],
                  'associated_2' => $simpleProducts['simple_not_visible']['general_sku']));
        $this->productHelper()->createProduct($groupedVisibleIndividual, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Bundle products
        $bundleNotAvailable = $this->loadDataSet('Product', 'fixed_bundle_visible');
        $bundleNotAvailable['bundle_items_data']['item_1'] = $this->loadDataSet('Product', 'bundle_item_2');
        $bundleNotAvailable['bundle_items_data']['item_1']['add_product_1']['bundle_items_search_sku'] =
            $simpleProducts['simpleOutOfStock']['general_sku'];
        $bundleNotAvailable['bundle_items_data']['item_1']['add_product_2']['bundle_items_search_sku'] =
            $simpleProducts['simpleDisabled']['general_sku'];
        $this->productHelper()->createProduct($bundleNotAvailable, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $bundleFixed = $this->loadDataSet('Product', 'fixed_bundle_visible');
        $bundleFixed['bundle_items_data']['item_1'] = $this->loadDataSet('Product', 'bundle_item_2');
        $bundleFixed['bundle_items_data']['item_1']['add_product_1']['bundle_items_search_sku'] =
            $simpleProducts['simple']['general_sku'];
        $bundleFixed['bundle_items_data']['item_1']['add_product_2']['bundle_items_search_sku'] =
            $simpleProducts['simpleWithBackorders']['general_sku'];
        $this->productHelper()->createProduct($bundleFixed, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $bundleDynamic = $this->loadDataSet('Product', 'dynamic_bundle_visible');
        $bundleDynamic['bundle_items_data']['item_1'] = $this->loadDataSet('Product', 'bundle_item_2');
        $bundleDynamic['bundle_items_data']['item_1']['add_product_1']['bundle_items_search_sku'] =
            $simpleProducts['simple']['general_sku'];
        $bundleDynamic['bundle_items_data']['item_1']['add_product_2']['bundle_items_search_sku'] =
            $simpleProducts['simpleWithBackorders']['general_sku'];
        $this->productHelper()->createProduct($bundleDynamic, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'                 => array('product_name' => $simpleProducts['simple']['general_name'],
                                              'sku' => $simpleProducts['simple']['general_sku'],
                                              'qty' => 1),
            'simpleWithBackorders'   => array('product_name' => $simpleProducts['simpleWithBackorders']['general_name'],
                                              'sku' => $simpleProducts['simpleWithBackorders']['general_sku'],
                                              'qty' => 2*$simpleProducts['simpleWithBackorders']['inventory_qty']),
            'simpleDisabled'         => array('sku' => $simpleProducts['simpleDisabled']['general_sku'],
                                              'qty' => 1),
            'nonExistentProduct'     => $this->loadDataSet('SkuProducts', 'non_existent_product'),
            'simpleCategory'         => array('sku' => $simpleProducts['simpleCategory']['general_sku'],
                                              'qty' => 1),
            'simpleWebsite'          => array('sku' => $simpleProducts['simpleWebsite']['general_sku'],
                                              'qty' => 1),
            'simpleOutOfStock'       => array('sku' => $simpleProducts['simpleOutOfStock']['general_sku'],
                                              'qty' => 1),
            'simpleNotVisible'       => array('product_name' => $simpleProducts['simple_not_visible']['general_name'],
                                              'sku' => $simpleProducts['simple_not_visible']['general_sku'],
                                              'qty' => 1),
            'simpleNotVisibleCustom' => array('sku' => $simpleProducts['simpleNotVisibleCustom']['general_sku'],
                                              'qty' => 1),
            'simpleNotRequiredCustom'=>array('product_name'=>$simpleProducts['simpleNotRequiredCustom']['general_name'],
                                             'sku' => $simpleProducts['simpleNotRequiredCustom']['general_sku'],
                                             'qty' => 1),
            'simpleRequiredCustom'   => array('product_name' => $simpleProducts['simpleRequiredCustom']['general_name'],
                                              'sku' => $simpleProducts['simpleRequiredCustom']['general_sku'],
                                              'qty' => 1,
                                              'Options' => array('option_1'=> array('parameters' => array (
                                              'title' => $customOptionsRequired['custom_options_general_title']),
                                              'options_to_choose' => array ('custom_option_dropdown' =>
                                              $customOptionsRequired['custom_option_row_1']['custom_options_title'])))),
            'simpleNotEnoughtQty'=> array('product_name' => $simpleProducts['simple']['general_name'],
                                          'sku' => $simpleProducts['simple']['general_sku'],
                                          'qty' => $simpleProducts['simple']['inventory_qty'] + 1),
            'simpleMin'          => array('product_name' => $simpleProducts['simple_min']['general_name'],
                                          'sku' => $simpleProducts['simple_min']['general_sku'],
                                          'qty' => $simpleProducts['simple_min']['inventory_min_allowed_qty'] - 1),
            'simpleMax'          => array('product_name' => $simpleProducts['simple_max']['general_name'],
                                          'sku' => $simpleProducts['simple_max']['general_sku'],
                                          'qty' => $simpleProducts['simple_max']['inventory_max_allowed_qty'] + 1),
            'simpleIncrement'    => array('product_name' => $simpleProducts['simple_increment']['general_name'],
                                          'sku' => $simpleProducts['simple_increment']['general_sku'],
                                          'qty' => $simpleProducts['simple_increment']['inventory_qty_increments'] + 1),
            'download'       => array('product_name' => $download['general_name'],
                                      'sku'          => $download['general_sku'],
                                      'qty'          => 1,
                                      'Options'      => array('option_1' => array ('parameters' => array (
                                      'title' => $download['downloadable_information_data']['downloadable_links_title'],
                                      'optionTitle' => $download['downloadable_information_data']['downloadable_link_1']
                                                                ['downloadable_link_row_title']),
                                      'options_to_choose' => array ('custom_option_checkbox' => 'Yes')))),
            'configurable'  => array('product_name'      => $configurable['general_name'],
                                     'sku'               => $configurable['general_sku'],
                                     'qty'               => 1,
                                     'Options'           => array ('option_1' => array('parameters' => array (
                                     'title'             => $attrData['admin_title']),
                                     'options_to_choose' => array ('custom_option_dropdown' =>
                                     $attrData['option_1']['store_view_titles']['Default Store View'])))),
            'grouped'       => array('product_name' => $grouped['general_name'],
                                     'sku'          => $grouped['general_sku'],
                                     'qty'          => 1,
                                     'Options'      => array ('option_1' => array ('parameters' => array (
                                     'subproductName' => $simpleProducts['simple']['general_name']),
                                     'options_to_choose' => array ('grouped_subproduct_qty' => '1')))),
            'groupedVisibleIndividual'  => array('product_name' => $groupedVisibleIndividual['general_name'],
                                             'sku'          => $groupedVisibleIndividual['general_sku'],
                                             'qty'          => 1,
                                             'Options'      => array ('option_1' => array ('parameters' => array (
                                             'subproductName' => $simpleProducts['simple_not_visible']['general_name']),
                                             'options_to_choose' => array ('grouped_subproduct_qty' => '1')))),
            'bundleNotAvailable'    => array('product_name' => $bundleNotAvailable['general_name'],
                                             'sku'          => $bundleNotAvailable['general_sku'],
                                             'qty'          => 1),
            'bundleFixed'           => array('product_name' => $bundleFixed['general_name'],
                                             'sku'          => $bundleFixed['general_sku'],
                                             'qty'          => 1,
                                             'Options'      => array('option_1' => array ('parameters' => array (
                                             'title' => $bundleNotAvailable['bundle_items_data']['item_1']
                                                                           ['bundle_items_default_title']),
                                             'options_to_choose' => array ('custom_option_dropdown' =>
                                             $simpleProducts['simple']['general_name'])))),
            'bundleDynamic'        => array('product_name' => $bundleDynamic['general_name'],
                                            'sku'          => $bundleDynamic['general_sku'],
                                            'qty'          => 1,
                                            'Options'      => array('option_1' => array ('parameters' => array (
                                            'title' => $bundleNotAvailable['bundle_items_data']['item_1']
                                                                          ['bundle_items_default_title']),
                                            'options_to_choose' => array ('custom_option_dropdown' =>
                                            $simpleProducts['simple']['general_name']))))
        );
    }

    /**
     * <p>Adding different product types using Order by SKU</p>
     *
     * @param string $productType
     * @param array $msgShoppingCart
     * @param array $msgAttentionGrid
     * @param array $data
     * @param array $customer
     *
     * @test
     * @dataProvider productListDataProvider
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-3952, TL-MAGE-3955, TL-MAGE-3952, TL-MAGE-3955, TL-MAGE-4006, TL-MAGE-4050,
     *             TL-MAGE-4007, TL-MAGE-5222, TL-MAGE-3996, TL-MAGE-5223, TL-MAGE-5224, TL-MAGE-5225,
     *             TL-MAGE-5226, TL-MAGE-3963, TL-MAGE-3965, TL-MAGE-3985, TL-MAGE-3966, TL-MAGE-3967,
     *             TL-MAGE-3969, TL-MAGE-3971, TL-MAGE-3999, TL-MAGE-4000, TL-MAGE-4004, TL-MAGE-4071
     */
    public function addProducts($productType, $msgShoppingCart, $msgAttentionGrid, $data, $customer)
    {
        //Preconditions
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->addBySkuHelper()->frontClearRequiredAttentionGrid();
        //Data
        $product = $data[$productType];
        //Steps
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $product['sku'], 'qty' => $product['qty']));
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
        $this->addBySkuHelper()->frontConfigureProduct($product, $productType, $msgShoppingCart, $msgAttentionGrid);
        if (!(($msgAttentionGrid['messageOne'] != 'null') && ($msgAttentionGrid['messageTwo'] == 'null'))) {
            switch ($productType) {
                case 'simpleNotVisible':
                    $this->assertTrue($this->shoppingCartHelper()->
                            frontShoppingCartHasProducts($product['product_name'], 'pageelement'),
                        'Product name: '.$product['product_name'].'is not present in shopping cart');
                    $this->assertFalse($this->controlIsVisible('link', 'edit'), 'Edit link is present. ');
                    break;
                case 'groupedVisibleIndividual':
                    $this->assertFalse($this->controlIsVisible('link', 'edit'), 'Edit link is present. ');
                    $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts(
                            $product['Options']['option_1']['parameters']['subproductName'], 'pageelement'),
                        'Product name: '. $product['Options']['option_1']['parameters']['subproductName'] .
                        'is not present in shopping cart');
                    break;
                case 'grouped':
                    $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts(
                            $product['Options']['option_1']['parameters']['subproductName'], 'pageelement'),
                        'Product name: '. $product['Options']['option_1']['parameters']['subproductName'] .
                        'is not present in shopping cart');
                    break;
                default:
                    $this->assertTrue($this->shoppingCartHelper()
                            ->frontShoppingCartHasProducts($product['product_name']),
                        'Product name: ' . $product['product_name'] . 'is not present in shopping cart');
                    break;
            }
            if ($msgAttentionGrid['messageOne'] == 'specify_option') {
                $this->addParameter('productName', $product['product_name']);
                $this->assertMessagePresent('success', 'product_added_to_cart');
            }
            $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
                'Products Requiring Attention section is present. ');
        }
    }

    /**
     * DataProvider for different product types and options
     *
     * @return array
     */
    public function productListDataProvider()
    {
        return array(
            array('simple', //simple product
                  array ('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                  array ('messageOne' => 'null', 'messageTwo' => 'null')),
            array('simpleWithBackorders', //simple product with backorders
                  array ('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                  array ('messageOne' => 'null', 'messageTwo' => 'null')),
            array('simpleDisabled', //simple disabled product
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'sku_not_found', 'messageTwo' => 'null')),
            array('nonExistentProduct', //nonexistent product
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'sku_not_found', 'messageTwo' => 'null')),
            array('simpleCategory', //simple product assigned to category with configured permissions [MAGETWO-1047]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'product_cannot_be_added', 'messageTwo' => 'null')),
            array('simpleWebsite', //simple product assigned to another website
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'sku_not_found', 'messageTwo' => 'null')),
            array('simpleOutOfStock', //simple product which is 'Out of Stock'
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'out_of_stock', 'messageTwo' => 'null')),
            array('simpleNotVisible', //simple product which Visibility is set to Not Visible Individually
                  array ('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                  array ('messageOne' => 'null', 'messageTwo' => 'null')),
            array('simpleNotVisibleCustom', //product with custom options,
                                            //which Visibility is set to Not Visible Individually
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'sku_not_found', 'messageTwo' => 'null')),
            array('simpleNotRequiredCustom', //simple product with custom options (is not required)
                  array ('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                  array ('messageOne' => 'null', 'messageTwo' => 'null')),
            array('simpleRequiredCustom', //simple product with custom options (is required) [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('simpleNotEnoughtQty', //product which there is no enough quantity in stock
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'qty_not_available', 'messageTwo' => 'left_in_stock')),
            array('simpleMin', //product with min qty = 5
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'requested_qty', 'messageTwo' => 'min_qty')),
            array('simpleMax', //product with max qty = 5
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'requested_qty', 'messageTwo' => 'max_qty')),
            array('simpleIncrement', //product with increment qty = 5 [MAGETWO-1541]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'requested_qty', 'messageTwo' => 'qty_increment')),
            array('download', // downloadable product [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('configurable', // configurable product with required options [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('grouped', // grouped product [MAGETWO-1466]
                  array('type' => 'error', 'text' =>  'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('groupedVisibleIndividual', // grouped product which Visibility is set to Not Visible Individually,
                                              //as subitem [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('bundleNotAvailable', //bundle with disabled and out of stock subitems
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'out_of_stock', 'messageTwo' => 'null')),
            array('bundleFixed', // fixed bundle product [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link')),
            array('bundleDynamic', // dynamic bundle product [MAGETWO-1466]
                  array('type' => 'error', 'text' => 'required_attention_product'),
                  array ('messageOne' => 'specify_option', 'messageTwo' => 'link'))
        );
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @depends addProducts
     * @TestlinkId TL-MAGE-3954
     */
    public function addMultipleSimpleProducts($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['simple']['sku'],
                                                              'qty' => $data['simple']['qty']), array('1', '2'));
        $this->clickButton('add_to_cart');
        //Verifying
        $this->addParameter('number', '2');
        $this->assertMessagePresent('success', 'products_added_to_cart_by_sku');
    }

    /**
     * <p>Successful and unsuccessful messages are located in frames different color</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4045
     */
    public function checkSuccessfulAndUnsuccessfulMessages($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['simple']['sku'],
                                                              'qty' => $data['simple']['qty']));
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['nonExistentProduct'], array('2'));
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
        $this->assertMessagePresent('error', 'required_attention_product');
    }

    /**
     * <p>Adding/Removing all items from Products Requiring Attention grid</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4234
     */
    public function removeAllProductFromAttentionGrid($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['nonExistentProduct']);
        $this->clickButton('add_to_cart');
        $this->clickButton('remove_all');
        //Verifying        
        $this->assertMessagePresent('success', 'items_removed');
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Products Requiring Attention section is present. ');
    }

    /**
     * <p>Adding/Removing each attention product separately</p>
     *
     * @param array $customer
     *
     * @test
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4235
     */
    public function removeAllProductsSeparately($customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $nonExistentProduct = $this->loadDataSet('SkuProducts', 'non_existent_product');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($nonExistentProduct);
        $this->clickButton('add_row', false);
        $nonExistentProduct = $this->loadDataSet('SkuProducts', 'non_existent_product');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($nonExistentProduct, array('2'));
        $this->clickButton('add_row', false);
        $nonExistentProduct = $this->loadDataSet('SkuProducts', 'non_existent_product');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($nonExistentProduct, array('3'));
        $this->clickButton('add_row', false);
        $nonExistentProduct = $this->loadDataSet('SkuProducts', 'non_existent_product');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($nonExistentProduct, array('4'));
        $this->clickButton('add_to_cart');
        $this->addBySkuHelper()->frontDeleteItems(array('4', '3', '2', '1'));
        //Verifying 
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Products Requiring Attention section is present. ');
    }
}
