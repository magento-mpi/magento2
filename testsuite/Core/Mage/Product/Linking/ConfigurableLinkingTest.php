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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for related, up-sell and cross-sell products.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Linking_ConfigurableLinkingTest extends Mage_Selenium_TestCase
{
    protected static $productTypes = array('simple',
                                           'virtual',
                                           'downloadable',
                                           'bundle',
                                           'configurable',
                                           'grouped');

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create attribute</p>
     *
     * @return array
     * @test
     */
    public function createAttribute()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadData('associated_attributes',
                                                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        //$this->addParameter('attributeName', 'Default');
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * <p>Preconditions</p>
     * <p>Create simple product for adding it to bundle and associated product</p>
     *
     * @param array $attrData
     *
     * @return string
     * @test
     * @depends createAttribute
     */
    public function createSimpleProductForBundle($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_sku'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products for linking in stock</p>
     *
     * @param array $attrData
     * @param string $simple
     *
     * @return array
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     */
    public function createProductsForLinkingInStock($attrData, $simple)
    {
        $productsInStock = array();
        $this->navigate('manage_products');
        foreach (self::$productTypes as $productType) {
            $productData = $this->loadData($productType . '_product_related',
                                           array('bundle_items_search_sku'     => $simple,
                                                'configurable_attribute_title' => $attrData['admin_title'],
                                                'associated_search_sku'        => $simple));
            $this->productHelper()->createProduct($productData, $productType);
            $this->assertMessagePresent('success', 'success_saved_product');

            $productsInStock[$productType]['general_name'] = $productData['general_name'];
            $productsInStock[$productType]['general_sku'] = $productData['general_sku'];
        }
        return $productsInStock;
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products for linking out of stock</p>
     *
     * @param array $attrData
     * @param string $simple
     *
     * @return array
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     */
    public function createProductsForLinkingOutOfStock($attrData, $simple)
    {
        $productsOutOfStock = array();
        $this->navigate('manage_products');
        foreach (self::$productTypes as $productType) {
            $productData = $this->loadData($productType . '_product_related',
                                           array('bundle_items_search_sku'     => $simple,
                                                'configurable_attribute_title' => $attrData['admin_title'],
                                                'associated_search_sku'        => $simple,
                                                'inventory_stock_availability' => 'Out of Stock'));
            $this->productHelper()->createProduct($productData, $productType);
            $this->assertMessagePresent('success', 'success_saved_product');

            $productsOutOfStock[$productType]['general_name'] = $productData['general_name'];
            $productsOutOfStock[$productType]['general_sku'] = $productData['general_sku'];
        }
        return $productsOutOfStock;
    }

    /**
     * <p>Review related products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock;
     *       Attach all types of products to the first one as related products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate names of related products in "related products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product contains block with related products;
     *    Names of related products are correct</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsInStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingInStock
     */
    public function relatedInStock($attrData, $simple, $productsInStock)
    {
        $productData1 = $this->loadData('configurable_product_for_linking_products',
                                        array('configurable_attribute_title' => $attrData['admin_title'],
                                             'associated_search_sku'         => $simple));
        $productData2 = $this->loadData('configurable_product_for_linking_products',
                                        array('configurable_attribute_title' => $attrData['admin_title'],
                                             'associated_search_sku'         => $simple));
        $i = 1;
        foreach ($productsInStock as $prod) {
            if ($i % 2) {
                $productData1['related_data']['related_' . $i++]['related_search_sku'] = $prod['general_sku'];
            } else {
                $productData2['related_data']['related_' . $i++]['related_search_sku'] = $prod['general_sku'];
            }

        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData1, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($productData2, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $i = 1;
        foreach ($productsInStock as $prod) {
            if ($i % 2) {
                $this->productHelper()->frontOpenProduct($productData1['general_name']);
            } else {
                $this->productHelper()->frontOpenProduct($productData2['general_name']);
            }
            $this->addParameter('productName', $prod['general_name']);
            if (!$this->controlIsPresent('link', 'related_product')) {
                $this->addVerificationMessage('Related Product ' . $prod['general_name'] . ' is not on the page');
            }
            $i++;
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review related products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock;
     *       Attach all types of products to the first one as related products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page for the first product;</p>
     * <p>4. Check if the first product contains any related products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product does not contains any related products;</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsOutOfStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingOutOfStock
     */
    public function relatedOutOfStock($attrData, $simple, $productsOutOfStock)
    {
        $productData = $this->loadData('configurable_product_for_linking_products',
                                       array('configurable_attribute_title' => $attrData['admin_title'],
                                            'associated_search_sku'         => $simple));
        $i = 1;
        foreach ($productsOutOfStock as $prod) {
            $productData['related_data']['related_' . $i++]['related_search_sku'] = $prod['general_sku'];
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        foreach ($productsOutOfStock as $prod) {
            $this->addParameter('productName', $prod['general_name']);
            if ($this->controlIsPresent('link', 'related_product')) {
                $this->addVerificationMessage('Related Product ' . $prod['general_name'] . ' is on the page');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock;
     *       Attach all types of products to the first one as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate names of cross-sell products in "cross-sell products block" in shopping cart;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product contains block with cross-sell products;
     *    Names of cross-sell products are correct</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsInStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingInStock
     */
    public function crossSellsInStock($attrData, $simple, $productsInStock)
    {
        $product1 = $this->loadDataSet('Product', 'configurable_product_for_linking_products',
                                       array('configurable_attribute_title' => $attrData['admin_title'],
                                            'associated_search_sku'         => $simple));
        $product2 = $this->loadDataSet('Product', 'configurable_product_for_linking_products',
                                       array('configurable_attribute_title' => $attrData['admin_title'],
                                            'associated_search_sku'         => $simple));

        $i = 1;
        foreach ($productsInStock as $prod) {
            if ($i % 2) {
                $product1['cross_sells_data']['cross_sells_' . $i++]['cross_sells_search_sku'] = $prod['general_sku'];
            } else {
                $product2['cross_sells_data']['cross_sells_' . $i++]['cross_sells_search_sku'] = $prod['general_sku'];
            }
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product1, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $i = 1;
        foreach ($productsInStock as $prod) {
            $this->addParameter('crosssellProductName', $prod['general_name']);
            $chooseOption = array('custom_option_select_attribute' => $attrData['option_1']['store_view_titles']['Default Store View']);
            if ($i % 2) {
                $this->productHelper()->frontOpenProduct($product1['general_name']);
                $param = $product1['associated_configurable_data']['associated_products_attribute_name'];
                $this->addParameter('title', $param);
            } else {
                $this->productHelper()->frontOpenProduct($product2['general_name']);
                $param = $product2['associated_configurable_data']['associated_products_attribute_name'];
                $this->addParameter('title', $param);
            }
            $this->fillForm($chooseOption);
            $this->productHelper()->frontAddProductToCart();
            if (!$this->controlIsPresent('link', 'crosssell_product')) {
                $this->addVerificationMessage('Cross-sell Product ' . $prod['general_name'] . ' is not on the page');
            }
            $this->shoppingCartHelper()->frontClearShoppingCart();
            $i++;
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable products in stock;
     *       Attach all types of products to the first one as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate that shopping cart page with the added product does not contains any cross-sell products;</p>
     * <p>Expected result:</p>
     * <p>Products are created.
     *    The configurable product in the shopping cart does not contain the cross-sell products</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsOutOfStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingOutOfStock
     */
    public function crossSellsOutOfStock($attrData, $simple, $productsOutOfStock)
    {
        $productData = $this->loadDataSet('Product', 'configurable_product_for_linking_products',
                                          array('configurable_attribute_title' => $attrData['admin_title'],
                                               'associated_search_sku'         => $simple));
        $param = $productData['associated_configurable_data']['associated_products_attribute_name'];
        $chooseOption = array('custom_option_select_attribute' => $attrData['option_1']['store_view_titles']['Default Store View']);
        $i = 1;
        foreach ($productsOutOfStock as $prod) {
            $productData['cross_sells_data']['cross_sells_' . $i++]['cross_sells_search_sku'] = $prod['general_sku'];
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        $this->addParameter('title', $param);
        $this->fillForm($chooseOption);
        $this->productHelper()->frontAddProductToCart();
        foreach ($productsOutOfStock as $prod) {
            $this->addParameter('crosssellProductName', $prod['general_name']);
            if ($this->controlIsPresent('link', 'crosssell_product')) {
                $this->addVerificationMessage('Cross-sell Product ' . $prod['general_name'] . ' is on the page');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock;
     *       Attach all types of products to the first one as up-sell products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate names of up-sell products in "up-sell products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created. The configurable product contains block with up-sell products;
     *    Names of up-sell products are correct</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsInStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingInStock
     */
    public function upSellsInStock($attrData, $simple, $productsInStock)
    {
        $productData1 = $this->loadData('configurable_product_for_linking_products',
                                        array('configurable_attribute_title' => $attrData['admin_title'],
                                             'associated_search_sku'         => $simple));
        $productData2 = $this->loadData('configurable_product_for_linking_products',
                                        array('configurable_attribute_title' => $attrData['admin_title'],
                                             'associated_search_sku'         => $simple));
        $i = 1;
        foreach ($productsInStock as $prod) {
            if ($i % 2) {
                $productData1['up_sells_data']['up_sells_' . $i++]['up_sells_search_sku'] = $prod['general_sku'];
            } else {
                $productData2['up_sells_data']['up_sells_' . $i++]['up_sells_search_sku'] = $prod['general_sku'];
            }

        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData1, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($productData2, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $i = 1;
        foreach ($productsInStock as $prod) {
            if ($i % 2) {
                $this->productHelper()->frontOpenProduct($productData1['general_name']);
            } else {
                $this->productHelper()->frontOpenProduct($productData2['general_name']);
            }
            $this->addParameter('productName', $prod['general_name']);
            if (!$this->controlIsPresent('link', 'upsell_product')) {
                $this->addVerificationMessage('Up-sell Product ' . $prod['general_name'] . ' is not on the page');
            }
            $i++;
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1.Create 1 configurable product in stock;
     *      Attach all types of products to the first one as up-sell products</p>
     * <p>2.Navigate to frontend;</p>
     * <p>3.Open product details page;</p>
     * <p>4.Validate that product details page for the 1st product does not contain up-sell block with the products</p>
     * <p>Expected result:</p>
     * <p>Products are created. The configurable product details page does not contain any up-sell product</p>
     *
     * @param array $attrData
     * @param string $simple
     * @param array $productsOutOfStock
     *
     * @test
     * @depends createAttribute
     * @depends createSimpleProductForBundle
     * @depends createProductsForLinkingOutOfStock
     */
    public function upSellsOutOfStock($attrData, $simple, $productsOutOfStock)
    {
        $productData = $this->loadData('configurable_product_for_linking_products',
                                       array('configurable_attribute_title' => $attrData['admin_title'],
                                            'associated_search_sku'         => $simple));
        $i = 1;
        foreach ($productsOutOfStock as $prod) {
            $productData['up_sells_data']['up_sells_' . $i++]['up_sells_search_sku'] = $prod['general_sku'];
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();

        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        foreach ($productsOutOfStock as $prod) {
            $this->addParameter('productName', $prod['general_name']);
            if ($this->controlIsPresent('link', 'upsell_product')) {
                $this->addVerificationMessage('Up-sell Product ' . $prod['general_name'] . ' is on the page');
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}
