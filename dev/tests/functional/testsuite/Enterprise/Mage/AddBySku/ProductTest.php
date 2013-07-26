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
 * Add by SKU functionality different product configurations on Frontend and Backend
 */
class Enterprise_Mage_AddBySku_ProductTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('The tests should be refactored.');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_all');
        $this->systemConfigurationHelper()->configure('CategoryPermissions/category_permissions_enable');
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
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
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
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['advanced_attribute_properties']['attribute_code'];
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrCode));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
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
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * Creates simple products for tests
     *
     * @param $customOptionsReq
     * @param $customOptionsNReq
     * @param $website
     * @param $category
     * @param $attrData
     * @return array
     */
    protected function _createSimpleProduct($customOptionsReq, $customOptionsNReq, $website, $category, $attrData)
    {
        $attributeCode = $attrData['advanced_attribute_properties']['attribute_code'];
        $simpleProducts = array();
        $simpleProducts['simple'] = $this->loadDataSet('Product', 'simple_sku');
        $simpleProducts['simple']['general_user_attr']['dropdown'][$attributeCode] =
            $attrData['option_1']['admin_option_name'];
        $simpleProducts['simpleWithBackorders'] = $this->loadDataSet('Product', 'simple_sku',
            array('inventory_backorders_default' => 'No', 'inventory_backorders' => 'Allow Qty Below 0'));
        $simpleProducts['simpleWithBackorders']['general_user_attr']['dropdown'][$attributeCode] =
            $attrData['option_2']['admin_option_name'];
        $simpleProducts['simpleDisabled'] = $this->loadDataSet('Product', 'simple_sku',
            array('product_online_status' => 'Disabled'));
        $simple['withCategory'] = $this->loadDataSet('Product', 'simple_sku',
            array('general_categories' => $category));
        $simple['withWebsite'] = $this->loadDataSet('Product', 'simple_sku', array('websites' => $website));
        $simple['simpleOutOfStock'] = $this->loadDataSet('Product', 'simple_sku', array(
            'general_stock_availability' => 'Out of Stock'
        ));
        $simple['notVisible'] = $this->loadDataSet('Product', 'simple_sku', array(
            'autosettings_visibility' => 'Not Visible Individually'
        ));
        $simple['notVisibleWithCustom'] = $this->loadDataSet('Product', 'simple_sku', array(
            'autosettings_visibility' => 'Not Visible Individually',
            'custom_options_data' => array($customOptionsReq)
        ));
        $simple['withNotRequiredCustom'] = $this->loadDataSet('Product', 'simple_sku', array(
            'custom_options_data' => array($customOptionsNReq)
        ));
        $simple['wittRequiredCustom'] = $this->loadDataSet('Product', 'simple_sku', array(
            'custom_options_data' => array($customOptionsReq)
        ));
        $simple['simpleMin'] = $this->loadDataSet('Product', 'simple_sku', array(
            'inventory_min_allowed_qty_default' => 'No',
            'inventory_min_allowed_qty' => '5'
        ));
        $simple['simpleMax'] = $this->loadDataSet('Product', 'simple_sku', array(
            'inventory_max_allowed_qty_default' => 'No',
            'inventory_max_allowed_qty' => '5'
        ));
        $simple['withIncrement'] = $this->loadDataSet('Product', 'simple_sku', array(
            'inventory_enable_qty_increments_default' => 'No',
            'inventory_enable_qty_increments' => 'Yes',
            'inventory_qty_increments_default' => 'No',
            'inventory_qty_increments' => '5'
        ));

        $this->navigate('manage_products');
        foreach ($simple as $product) {
            $this->productHelper()->createProduct($product);
            $this->assertMessagePresent('success', 'success_saved_product');
        }

        return $simple;
    }

    /**
     * <p>Create all products for testing SKU functionality</p>
     *
     * @return array
     *
     * @test
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function preconditionsForTests()
    {
        $website = $this->loadDataSet('Website', 'generic_website');
        $store = $this->loadDataSet('Store', 'generic_store', array('website' => $website['website_name']));
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $store['store_name']));
        $category = $this->loadDataSet('Category', 'sub_category_required_permissions_deny');
        $categoryPath = $category['parent_category'] . '/' . $category['name'];
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['attribute_code']));
        //Preconditions
        $this->loginAdminUser();
        //Create Website
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($website, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($store, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Create category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create and assign attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Data
        //Custom options
        $customOptionsReq = $this->loadDataSet('Product', 'custom_options_dropdown');
        $customOptionsNReq = $this->loadDataSet('Product', 'custom_options_dropdown',
            array('custom_options_general_is_required' => 'No'));
        //Simple
        $simple = $this->_createSimpleProduct($customOptionsReq, $customOptionsNReq,
            $website['website_name'], $categoryPath, $attrData);
        //Configurable        
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array(
                'associated_name' => $simple['simple']['general_name'],
                'associated_sku' => $simple['simple']['general_sku']
            ),
            array(
                'var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                'general_attribute_1' => $attrData['attribute_properties']['attribute_label']
            )
        );
        //Downloadable
        $download = $this->loadDataSet('Product', 'downloadable_product_visible',
            array('downloadable_link_2' => $this->loadDataSet('Product', 'downloadable_links')));
        //Grouped
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', null, array(
            'associated_1' => $simple['simple']['general_sku'],
            'associated_2' => $simple['withBackorders']['general_sku']
        ));
        $groupedVisibleInd = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', null, array(
            'associated_1' => $simple['simple']['general_sku'],
            'associated_2' => $simple['notVisible']['general_sku']
        ));
        //Bundle
        $bundleItemNotAvailable = $this->loadDataSet('Product', 'bundle_item_1', null, array(
            'bundleSku1' => $simple['simpleOutOfStock']['general_sku'],
            'bundleSku2' => $simple['disabled']['general_sku']
        ));
        $bundleItemFixed = $this->loadDataSet('Product', 'bundle_item_1', null, array(
            'bundleSku1' => $simple['simple']['general_sku'],
            'bundleSku2' => $simple['withBackorders']['general_sku']
        ));
        $bundleItemDynamic = $this->loadDataSet('Product', 'bundle_item_1', null, array(
            'bundleSku1' => $simple['simple']['general_sku'],
            'bundleSku2' => $simple['withBackorders']['general_sku']
        ));
        $bundleNotAvailable = $this->loadDataSet('Product', 'fixed_bundle_visible',
            array('item_1' => $bundleItemNotAvailable));
        $bundleFixed = $this->loadDataSet('Product', 'fixed_bundle_visible', array('item_1' => $bundleItemFixed));
        $bundleDynamic = $this->loadDataSet('Product', 'dynamic_bundle_visible', array('item_1' => $bundleItemDynamic));
        //Steps
        $this->productHelper()->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($groupedVisibleInd, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundleNotAvailable, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundleFixed, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundleDynamic, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');

        $productData = $this->_returnSimple($simple, $customOptionsReq);
        $downOption = $download['downloadable_information_data']['downloadable_link_1']['downloadable_link_row_title'];

        $productData['download'] = array(
            'product_name' => $download['general_name'],
            'sku' => $download['general_sku'],
            'qty' => 1,
            'Options' => array('option_1' => array(
                'parameters' => array(
                    'title' => $download['downloadable_information_data']['downloadable_links_title'],
                    'optionTitle' => $downOption
                ),
                'options_to_choose' => array('custom_option_checkbox' => 'Yes')
            )),
            'Options_backend' => array('option_1' => array(
                'title' => $download['downloadable_information_data']['downloadable_links_title'],
                'field_link' => array(
                    'fieldType' => 'checkbox',
                    'fieldsParameter' => $download['downloadable_information_data']['downloadable_link_1'],
                    'fieldsValue' => 'Yes'
                ))
            )
        );
        $productData['configurable'] = array(
            'product_name' => $configurable['general_name'],
            'sku' => $configurable['general_sku'],
            'qty' => 1,
            'Options' => array(
                'option_1' => array(
                    'parameters' => array(
                        'title' => $attrData['attribute_properties']['attribute_label']),
                    'options_to_choose' => array(
                        'custom_option_dropdown' => $attrData['option_1']['store_view_titles']['Default Store View']
                    )
                )
            ),
            'Options_backend' => array(
                'option_1' => array(
                    'title' => $attrData['attribute_properties']['attribute_label'],
                    'field_dropdown' => array(
                        'fieldType' => 'dropdown',
                        'fieldsValue' => $attrData['option_1']['admin_option_name']
                    )
                )
            )
        );
        $productData['grouped'] = array(
            'product_name' => $grouped['general_name'],
            'sku' => $grouped['general_sku'],
            'qty' => 1,
            'Options' => array('option_1' => array(
                'parameters' => array('subproductName' => $simple['simple']['general_name']),
                'options_to_choose' => array('grouped_subproduct_qty' => '1')
            )),
            'Options_backend' => array('option_1' => array(
                'title' => $simple['simple']['general_name'],
                'field_dropdown' => array(
                    'fieldType' => 'field',
                    'fieldParameter' => $simple['simple']['general_name'],
                    'fieldsValue' => '1'
                )
            ))
        );
        $productData['groupedVisibleIndividual'] = array(
            'product_name' => $groupedVisibleInd['general_name'],
            'sku' => $groupedVisibleInd['general_sku'],
            'qty' => 1,
            'Options' => array('option_1' => array(
                'parameters' => array('subproductName' => $simple['notVisible']['general_name']),
                'options_to_choose' => array('grouped_subproduct_qty' => '1')
            ))
        );
        $productData['bundleNotAvailable'] = array(
            'product_name' => $bundleNotAvailable['general_name'],
            'sku' => $bundleNotAvailable['general_sku'],
            'qty' => 1
        );
        $productData['bundleFixed'] = array(
            'product_name' => $bundleFixed['general_name'],
            'sku' => $bundleFixed['general_sku'],
            'qty' => 1,
            'Options' => array('option_1' => array(
                'parameters' => array(
                    'title' => $bundleFixed['general_bundle_items']['item_1']['bundle_items_default_title']
                ),
                'options_to_choose' => array('custom_option_dropdown' => $simple['simple']['general_name'])
            )),
            'Options_backend' => array('option_1' => array(
                'title' => 'Drop-down',
                'field_dropdown' => array(
                    'fieldType' => 'dropdown',
                    'fieldsValue' => $simple['simple']['general_name']
                )
            ))
        );
        $productData['bundleDynamic'] = array(
            'product_name' => $bundleDynamic['general_name'],
            'sku' => $bundleDynamic['general_sku'],
            'qty' => 1,
            'Options' => array('option_1' => array(
                'parameters' => array(
                    'title' => $bundleDynamic['general_bundle_items']['item_1']['bundle_items_default_title']
                ),
                'options_to_choose' => array('custom_option_dropdown' => $simple['simple']['general_name'])
            )),
            'Options_backend' => array('option_1' => array(
                'title' => 'Drop-down',
                'field_dropdown' => array(
                    'fieldType' => 'dropdown',
                    'fieldsValue' => $simple['simple']['general_name']
                )
            ))
        );
        return $productData;
    }

    /**
     * Form array with simple product data for return
     *
     * @param $simple
     * @param $customOptionsReq
     * @return array
     */
    protected function _returnSimple($simple, $customOptionsReq)
    {
        return array(
            'simple' => array(
                'product_name' => $simple['simple']['general_name'],
                'sku' => $simple['simple']['general_sku'],
                'qty' => 1
            ),
            'simpleQtyEmpty' => array(
                'product_name' => $simple['simple']['general_name'],
                'sku' => $simple['simple']['general_sku'],
                'qty' => 0
            ),
            'withBackorders' => array(
                'product_name' => $simple['withBackorders']['general_name'],
                'sku' => $simple['withBackorders']['general_sku'],
                'qty' => 2 * $simple['withBackorders']['general_qty']
            ),
            'disabled' => array(
                'sku' => $simple['disabled']['general_sku'],
                'qty' => 1
            ),
            'nonExistentProduct' => array(
                'sku' => $this->generate('string', 10, ':alnum:'),
                'qty' => 1
            ),
            'withCategory' => array(
                'sku' => $simple['withCategory']['general_sku'],
                'qty' => 1
            ),
            'withWebsite' => array(
                'sku' => $simple['withWebsite']['general_sku'],
                'qty' => 1
            ),
            'simpleOutOfStock' => array(
                'sku' => $simple['simpleOutOfStock']['general_sku'],
                'qty' => 1
            ),
            'simpleNotVisible' => array(
                'product_name' => $simple['notVisible']['general_name'],
                'sku' => $simple['notVisible']['general_sku'],
                'qty' => 1
            ),
            'notVisibleWithCustom' => array(
                'sku' => $simple['notVisibleWithCustom']['general_sku'],
                'qty' => 1
            ),
            'withNotRequiredCustom' => array(
                'product_name' => $simple['withNotRequiredCustom']['general_name'],
                'sku' => $simple['withNotRequiredCustom']['general_sku'],
                'qty' => 1
            ),
            'wittRequiredCustom' => array(
                'product_name' => $simple['wittRequiredCustom']['general_name'],
                'sku' => $simple['wittRequiredCustom']['general_sku'],
                'qty' => 1,
                'Options' => array(
                    'option_1' => array(
                        'parameters' => array('title' => $customOptionsReq['custom_options_general_title']),
                        'options_to_choose' => array(
                            'custom_option_dropdown' => $customOptionsReq['custom_option_row_1']['custom_options_title']
                        )
                    )
                ),
                'Options_backend' => array(
                    'option_1' => array(
                        'title' => $customOptionsReq['custom_options_general_title'],
                        'field_dropdown' => array(
                            'fieldType' => 'dropdown',
                            'fieldsValue' => $customOptionsReq['custom_option_row_1']['custom_options_title']
                        )
                    )
                )
            ),
            'simpleNotEnoughQty' => array(
                'product_name' => $simple['simple']['general_name'],
                'sku' => $simple['simple']['general_sku'],
                'qty' => $simple['simple']['general_qty'] + 1
            ),
            'simpleMin' => array(
                'product_name' => $simple['simpleMin']['general_name'],
                'sku' => $simple['simpleMin']['general_sku'],
                'qty' => $simple['simpleMin']['inventory_min_allowed_qty'] - 1
            ),
            'simpleMax' => array(
                'product_name' => $simple['simpleMax']['general_name'],
                'sku' => $simple['simpleMax']['general_sku'],
                'qty' => $simple['simpleMax']['inventory_max_allowed_qty'] + 1
            ),
            'simpleIncrement' => array(
                'product_name' => $simple['withIncrement']['general_name'],
                'sku' => $simple['withIncrement']['general_sku'],
                'qty' => $simple['withIncrement']['inventory_qty_increments'] + 1
            )
        );
    }

    //-------------------------------------------Frontend. Shopping Cart.-----------------------------------------------
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
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->addBySkuHelper()->frontClearRequiredAttentionGrid();
        //Data
        $product = $data[$productType];
        //Steps
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(
            array(array('sku' => $product['sku'], 'qty' => $product['qty']))
        );
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
        $this->addBySkuHelper()->frontConfigureProduct($product, $productType, $msgShoppingCart, $msgAttentionGrid);
        if (!(($msgAttentionGrid['messageOne'] != 'null') && ($msgAttentionGrid['messageTwo'] == 'null'))) {
            switch ($productType) {
                case 'simpleNotVisible':
                    $name = $product['product_name'];
                    $this->assertTrue(
                        $this->shoppingCartHelper()->frontShoppingCartHasProducts($name, 'pageelement'),
                        'Product name: ' . $name . ' is not present in shopping cart'
                    );
                    $this->assertFalse($this->controlIsVisible('link', 'edit'), 'Edit link is present.');
                    break;
                case 'groupedVisibleIndividual':
                    $name = $product['Options']['option_1']['parameters']['subproductName'];
                    $this->assertTrue(
                        $this->shoppingCartHelper()->frontShoppingCartHasProducts($name, 'pageelement'),
                        'Product name: ' . $name . ' is not present in shopping cart'
                    );
                    $this->assertFalse($this->controlIsVisible('link', 'edit'), 'Edit link is present.');
                    break;
                case 'grouped':
                    $name = $product['Options']['option_1']['parameters']['subproductName'];
                    $this->assertTrue(
                        $this->shoppingCartHelper()->frontShoppingCartHasProducts($name, 'pageelement'),
                        'Product name: ' . $name . ' is not present in shopping cart'
                    );
                    break;
                default:
                    $name = $product['product_name'];
                    $this->assertTrue(
                        $this->shoppingCartHelper()->frontShoppingCartHasProducts($name),
                        'Product name: ' . $name . ' is not present in shopping cart'
                    );
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
            array(
                'simple', //simple product
                array('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                array('messageOne' => 'null', 'messageTwo' => 'null')
            ),
            array(
                'withBackorders', //simple product with backorders
                array('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                array('messageOne' => 'null', 'messageTwo' => 'null')
            ),
            array(
                'disabled', //simple disabled product
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'sku_not_found', 'messageTwo' => 'null')
            ),
            array(
                'nonExistentProduct', //nonexistent product
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'sku_not_found', 'messageTwo' => 'null')
            ),
            array(
                'withCategory', //simple product assigned to category with configured permissions [MAGETWO-3390]
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'product_cannot_be_added', 'messageTwo' => 'null')
            ),
            array(
                'withWebsite', //simple product assigned to another website
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'sku_not_found', 'messageTwo' => 'null')
            ),
            array(
                'simpleOutOfStock', //simple product which is 'Out of Stock'
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'out_of_stock', 'messageTwo' => 'null')
            ),
            array(
                'simpleNotVisible', //simple product which Visibility is set to Not Visible Individually
                array('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                array('messageOne' => 'null', 'messageTwo' => 'null')
            ),
            array(
                'notVisibleWithCustom', //product with custom options, which Visibility - Not Visible Individually
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'sku_not_found', 'messageTwo' => 'null')
            ),
            array(
                'withNotRequiredCustom', //simple product with custom options (is not required)
                array('type' => 'success', 'text' => 'product_added_to_cart_by_sku'),
                array('messageOne' => 'null', 'messageTwo' => 'null')
            ),
            array(
                'wittRequiredCustom', //simple product with custom options (is required)
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'simpleNotEnoughQty', //product which there is no enough quantity in stock
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'qty_not_available', 'messageTwo' => 'left_in_stock')
            ),
            array(
                'simpleMin', //product with min qty = 5
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'requested_qty', 'messageTwo' => 'min_qty')
            ),
            array(
                'simpleMax', //product with max qty = 5
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'requested_qty', 'messageTwo' => 'max_qty')
            ),
            array(
                'simpleIncrement', //product with increment qty = 5 [MAGETWO-1541]
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'requested_qty', 'messageTwo' => 'qty_increment')
            ),
            array(
                'download', // downloadable product
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'configurable', // configurable product with required options
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'grouped',
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'groupedVisibleIndividual', // grouped product with subItem Visibility - Not Visible Individually)
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'bundleNotAvailable', //bundle with disabled and out of stock subItems
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'out_of_stock', 'messageTwo' => 'null')
            ),
            array(
                'bundleFixed', // fixed bundle product
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            ),
            array(
                'bundleDynamic', // dynamic bundle product
                array('type' => 'error', 'text' => 'required_attention_product'),
                array('messageOne' => 'specify_option', 'messageTwo' => 'link')
            )
        );
    }

    //------------------------------------Backend. Customer. Manage Shopping Cart.--------------------------------------
    /**
     * <p>Adding products for which configuration is not required</p>
     *
     * @param string $productType
     * @param array $msgShoppingCart
     * @param string $msgAttentionGrid
     * @param string $rule
     * @param array $data
     * @param array $customer
     *
     * @test
     * @dataProvider productListWithoutConfigureCustomerDataProvider
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-5229, TL-MAGE-4174, TL-MAGE-5230, TL-MAGE-5231, TL-MAGE-4085,
     *             TL-MAGE-4127, TL-MAGE-4130, TL-MAGE-4131, TL-MAGE-4134, TL-MAGE-4052
     */
    public function productBackendCustomer($productType, $msgShoppingCart, $msgAttentionGrid, $rule, $data, $customer)
    {
        //Data
        $product = $data[$productType];
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $customer['email']));
        $this->addBySkuHelper()->openShoppingCart();
        $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        $this->addBySkuHelper()->removeAllItemsFromShoppingCart();
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array($product));
        //Verifying
        $this->_verifyProductsInAttentionGrid($product, $msgShoppingCart, $msgAttentionGrid, $rule);
    }

    /**
     * <p>Adding products for which configuration is required.</p>
     *
     * @param string $productType
     * @param array $msgShoppingCart
     * @param string $msgAttentionGrid
     * @param array $data
     * @param array $customer
     *
     * @test
     * @dataProvider productListWithConfigureDataProvider
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4089, TL-MAGE-4095, TL-MAGE-4096, TL-MAGE-4099, TL-MAGE-4101, TL-MAGE-4104
     */
    public function productConfigureBackendCustomer($productType, $msgShoppingCart, $msgAttentionGrid, $data, $customer)
    {
        //Data
        $product = $data[$productType];
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $customer['email']));
        $this->addBySkuHelper()->openShoppingCart();
        $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        $this->addBySkuHelper()->removeAllItemsFromShoppingCart();
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array($product));
        //Verifying
        $this->addParameter('number', '1');
        $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
        $this->assertMessagePresent('error', $msgAttentionGrid);
        $this->addBySkuHelper()->configureProduct($product);
        $this->clickButton('add_to_cart_from_error', true);
        $this->pleaseWait();
        $this->assertTrue(!$this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Required Attention grid is present.');
        $gotData = $this->addBySkuHelper()->getProductInfoInTable();
        if ($productType == 'grouped') {
            $this->assertEquals(trim($gotData['product_1']['product_name']),
                $product['Options_backend']['option_1']['field_dropdown']['fieldParameter']);
        } else {
            $this->assertEquals(trim($gotData['product_1']['product_name']), $product['product_name']);
            $this->assertEquals($gotData['product_1']['qty'], $product['qty']);
        }
    }

    /**
     * DataProvider for different product types on Manage Shopping Cart page  (without item's configuration)
     *
     * @return array
     */
    public function productListWithoutConfigureCustomerDataProvider()
    {
        return array(
            //simple product, TL-MAGE-4052
            array('simple', null, null, 'noMessage'),
            //simple disabled product, TL-MAGE-5229
            array('disabled', array('type' => 'error', 'text' => 'required_attention_product'), 'disabled_product',
                'attention'),
            //nonexistent product, TL-MAGE-4174
            array('nonExistentProduct', array('type' => 'error', 'text' => 'required_attention_product'),
                'sku_not_found', 'attention'),
            //simple product assigned to another website, TL-MAGE-5230
            array('withWebsite', array('type' => 'error', 'text' => 'required_attention_product'),
                'sku_assigned_another_website', 'attention'),
            //simple product which is 'Out of Stock', TL-MAGE-5231
            array('simpleOutOfStock', array('type' => 'error', 'text' => 'required_attention_product'), 'out_of_stock',
                'attention'),
            //with non-required custom options, TL-MAGE-4085
            array('withNotRequiredCustom', null, null, 'enableConfigure'),
            //product which there is no enough quantity in stock, TL-MAGE-4127
            array('simpleNotEnoughQty', array('type' => 'error', 'text' => 'qty_not_available'), null, 'productName'),
            //product with qty less than minimum allowed in cart qty = 5, TL-MAGE-4130
            array('simpleMin', array('type' => 'error', 'text' => 'min_qty'), null, 'qty'),
            //product with qty more than maximum allowed in cart max qty = 5, TL-MAGE-4131
            array('simpleMax', array('type' => 'error', 'text' => 'max_qty'), null, 'qty'),
            //product with qty is not multiple to Qty Increments = 5, TL-MAGE-4134
            array('simpleIncrement', array('type' => 'error', 'text' => 'qty_increment'), null, 'qty')
        );
    }

    //---------------------------------------------------Backend. Sales. Orders.----------------------------------------
    /**
     * <p>Adding products for which configuration is not required</p>
     *
     * @param string $productType
     * @param mixed $msgShoppingCart
     * @param string $msgAttentionGrid
     * @param string $rule
     * @param array $data
     *
     * @test
     * @dataProvider productListWithoutConfigureOrderDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4179, TL-MAGE-5243, TL-MAGE-4197, TL-MAGE-4123, TL-MAGE-4145,
     *             TL-MAGE-4148, TL-MAGE-4150, TL-MAGE-4151, TL-MAGE-4152, TL-MAGE-4171
     */
    public function productBackendOrder($productType, $msgShoppingCart, $msgAttentionGrid, $rule, $data)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Steps
        $product = $data[$productType];
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array($product), true, false);
        //Verifying
        $this->_verifyProductsInAttentionGrid($product, $msgShoppingCart, $msgAttentionGrid, $rule);
    }

    /**
     * DataProvider for different product types and its behaviour in backend order by SKU (without item's configuration)
     *
     * @return array
     */
    public function productListWithoutConfigureOrderDataProvider()
    {
        return array(
            //simple product, TL-MAGE-4197
            array('simple', null, null, 'noMessage'),
            //simple disabled product, TL-MAGE-4171
            array('disabled', array('type' => 'error', 'text' => 'disabled_product'), null, 'sku'),
            //nonexistent product, TL-MAGE-4179
            array('nonExistentProduct', array('type' => 'error', 'text' => 'required_attention_product'),
                'sku_not_found', 'attention'),
            //simple product assigned to another website, TL-MAGE-5243
            array('withWebsite', array('type' => 'error', 'text' => 'required_attention_product'),
                'sku_assigned_another_website', 'attention'),
            //simple product which is 'Out of Stock', TL-MAGE-4145
            array('simpleOutOfStock', array('type' => 'error', 'text' => 'out_of_stock'), null, 'sku'),
            //with non-required custom options, TL-MAGE-4123
            array('withNotRequiredCustom', null, null, 'enableConfigure'),
            //product which there is no enough quantity in stock, TL-MAGE-4148
            array('simpleNotEnoughQty', array('type' => 'error', 'text' => 'qty_not_available'), null, 'productName'),
            //product with qty less than minimum allowed in cart qty = 5, TL-MAGE-4150
            array('simpleMin', array('type' => 'error', 'text' => 'min_qty'), null, 'qty'),
            //product with qty more than maximum allowed in cart max qty = 5, TL-MAGE-4151
            array('simpleMax', array('type' => 'error', 'text' => 'max_qty'), null, 'qty'),
            //product with qty is not multiple to Qty Increments = 5, TL-MAGE-4152
            array('simpleIncrement', array('type' => 'error', 'text' => 'qty_increment'), null, 'qty')
        );
    }

    /**
     * <p>Adding products for which configuration is required.</p>
     *
     * @param string $productType
     * @param array $msgShoppingCart
     * @param string $msgAttentionGrid
     * @param array $data
     *
     * @test
     * @dataProvider productListWithConfigureDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4094, TL-MAGE-4113, TL-MAGE-4115, TL-MAGE-4116, TL-MAGE-4117, TL-MAGE-4119
     */
    public function productConfigureBackendOrder($productType, $msgShoppingCart, $msgAttentionGrid, $data)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Steps
        $product = $data[$productType];
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array($product), true, false);
        //Verifying
        $this->addParameter('number', '1');
        $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
        $this->assertMessagePresent('error', $msgAttentionGrid);
        $this->addBySkuHelper()->configureProduct($product);
        $this->clickButton('add_to_order_from_error', true);
        $this->pleaseWait();
        $this->assertTrue(!$this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Required Attention grid is present.');
        $gotData = $this->addBySkuHelper()->getProductInfoInTable();
        if ($productType == 'grouped') {
            $this->assertEquals(trim($gotData['product_1']['product_name']),
                $product['Options_backend']['option_1']['field_dropdown']['fieldParameter']);
        } else {
            $this->assertEquals(trim($gotData['product_1']['product_name']), $product['product_name']);
            $this->assertEquals($gotData['product_1']['qty'], $product['qty']);
        }
    }

    /**
     * DataProvider for different product types and its behaviour in backend order by SKU (with item's configuration)
     *
     * @return array
     */
    public function productListWithConfigureDataProvider()
    {
        return array(
            //simple product with custom options (is required), TL-MAGE-4094
            array('wittRequiredCustom',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option'),
            // downloadable product, TL-MAGE-4113
            array('download',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option'),
            // configurable product with required options, TL-MAGE-4115
            array('configurable',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option'),
            // grouped product, TL-MAGE-4116
            array('grouped',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option'),
            // fixed bundle product, TL-MAGE-4117
            array('bundleFixed',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option'),
            // dynamic bundle product, TL-MAGE-4119
            array('bundleDynamic',
                array('type' => 'error', 'text' => 'required_attention_product'), 'specify_option')
        );
    }

    /**
     * <p>Adding to Order by SKU after entering values in multiple fields</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4198
     */
    public function backendOrderMultipleProducts($data)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        $simple = $data['simple'];
        $simpleNotReqCustom = $data['withNotRequiredCustom'];
        $productNonExist = array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 1);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array(
            $simple, $simpleNotReqCustom, $productNonExist), true, false);
        //Verifying
        $this->addParameter('number', '1');
        $this->assertMessagePresent('error', 'required_attention_product');
        $gotData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->assertEquals($gotData['product_1']['product_sku'], $productNonExist['sku']);
        $gotData = $this->addBySkuHelper()->getProductInfoInTable();
        $this->assertEquals($gotData['product_1']['product_sku'], $data['simple']['sku']);
        $this->assertEquals($gotData['product_1']['qty'], $data['simple']['qty']);
        $this->assertEquals($gotData['product_2']['product_sku'], $data['withNotRequiredCustom']['sku']);
        $this->assertEquals($gotData['product_2']['qty'], $data['withNotRequiredCustom']['qty']);
    }

    /**
     * <p>QTY field validation</p>
     *
     * @param array $data
     * @param string $qtySku
     *
     * @test
     * @dataProvider qtySkuDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5241
     */
    public function backendOrderQtyValidation($qtySku, $data)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        $product = array('sku' => $data['simple']['sku'], 'qty' => $qtySku);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart(array($product), true, false);
        $this->addParameter('number', '1');
        $this->assertMessagePresent('error', 'required_attention_product');
        $gotData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->assertEquals($gotData['product_1']['product_sku'], $product['sku']);
        $this->assertMessagePresent('error', 'qty_sku_invalid_number');
        $this->addParameter('rowIndex', '1');
        $this->assertTrue($this->controlIsPresent('field', 'qty'), 'Qty field is disabled.');
    }

    /**
     * DataProvider for validation Qty field in Add By SKU fieldset
     *
     * @return array
     */
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
     * Verify products in attention in backend area
     *
     * @param array $product
     * @param array $msgShoppingCart
     * @param string $msgAttentionGrid
     * @param string $rule
     */
    protected function _verifyProductsInAttentionGrid($product, $msgShoppingCart, $msgAttentionGrid, $rule)
    {
        if ($rule === 'attention') {
            $this->addParameter('number', '1');
            $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
            $gotData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
            $this->assertEquals($gotData['product_1']['product_sku'], $product['sku']);
            $this->assertMessagePresent('error', $msgAttentionGrid);
            $this->addParameter('rowIndex', '1');
            $this->assertTrue(!$this->controlIsEditable('field', 'qty'), 'Qty field is not disabled. ');
        } elseif ($rule === 'enableConfigure') {
            $this->addParameter('sku', $product['sku']);
            $this->assertTrue($this->buttonIsPresent('configure_order_item'));
        } else {
            if (!is_null($msgShoppingCart['type'])) {
                $this->assertMessagePresent($msgShoppingCart['type'], $msgShoppingCart['text']);
            }
            switch ($rule) {
                case 'sku':
                    $this->addParameter('sku', $product['sku']);
                    break;
                case 'qty':
                    $this->addParameter('qty', 5);
                    break;
                case 'productName':
                    $this->addParameter('productName', $product['product_name']);
                    break;
                default:
                    break;
            }
            $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
                'Required Attention grid is present');
            $this->addParameter('number', '1');
            $gotData = $this->addBySkuHelper()->getProductInfoInTable();
            $this->assertEquals($gotData['product_1']['product_sku'], $product['sku']);
            $this->assertEquals($gotData['product_1']['qty'], $product['qty']);
        }
    }
}
