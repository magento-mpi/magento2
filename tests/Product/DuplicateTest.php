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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_DuplicateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }


    /**
     * <p>Creating duplicated simple product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function duplicateSimple()
    {
        //Data
        $productData = $this->loadData('simple_product_required', null, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $productData['general_name'],
                      'product_sku' => $productData['general_sku']));
                  print_r($productData);
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        return $productData;
    }

    /**
     * <p>Creating duplicated virtual product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function duplicateVirtual()
    {
        //Data
        $productData = $this->loadData('virtual_product_required', null, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $productData['general_name'],
                      'product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        return $productData;
    }

    /**
     * <p>Creating duplicated downloadable product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function duplicateDownloadable()
    {
        //Data
        $productData = $this->loadData('downloadable_product_required', null, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $productData['general_name'],
                      'product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        return $productData;
    }

    /**
     * <p>Creating grouped product with assosiated downloadable </p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateDownloadable
     * @test
     */
    public function duplicateGroupedDownloadable($downloadableData)
    {
        //Data
        print_r($downloadableData);
        $groupedData = $this->loadData('grouped_product_required',
                array('associated_products_sku' => $downloadableData['general_sku']),
                array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $groupedData['general_name'],
                      'product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }

    /**
     * <p>Creating duplicated Grouped product with assosiated Virtual</p>
     * <p>Preconditions:</p>
     * <p>Virtual product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateVirtual
     * @test
     */
    public function duplicateGroupedVirtual($virtualData)
    {
        //Data
        $groupedData = $this->loadData('grouped_product_required',
                array('associated_products_sku' => $virtualData['general_sku']), array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $groupedData['general_name'],
                      'product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }

    /**
     * <p>Creating duplicated Grouped product with assosiated Phisical Simple</p>
     * <p>Preconditions:</p>
     * <p>Phisical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateSimple
     * @test
     */
    public function duplicateGroupedPhysical($simpleData)
    {
        //Data
        $groupedData = $this->loadData('grouped_product_required',
                array('associated_products_sku' => $simpleData['general_sku']), array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $groupedData['general_name'],
                      'product_sku' => $groupedData['general_sku']));

        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }

    /**
     * <p>Creating duplicated Grouped product with assosiated Simple, Virtual, Downloadable</p>
     * <p>Preconditions:</p>
     * <p>Simple, Virtual, Downloadable products created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateSimple
     * @depends duplicateVirtual
     * @depends duplicateDownloadable
     * @test
     */
    public function duplicateGroupedCombined($simpleData, $virtualData, $downloadableData)
    {
        //Data
        $groupedData = $this->loadData('grouped_product_required',
                array('associated_products_sku' => $simpleData['general_sku']), array('general_name', 'general_sku'));
        $groupedData['associated_products_grouped_data']['associated_products_grouped_2'] =
                $this->loadData('associated_products_grouped',
                        array('associated_products_sku' => $virtualData['general_sku']));
        $groupedData['associated_products_grouped_data']['associated_products_grouped_3'] =
                $this->loadData('associated_products_grouped',
                        array('associated_products_sku' => $downloadableData['general_sku']));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $groupedData['general_name'],
                      'product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }

    /**
     * <p>Duplicate Configurable product with assosiated Virtual</p>
     * <p>Preconditions</p>
     * <p>Attribute Set created</p>
     * <p>Virtual product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all required fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Virtual product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     *
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function duplicateConfigurableVirtual()
    {

        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);

        $this->assertPreConditions();

        $virtualData = $this->loadData('virtual_product_required', null, array('general_name', 'general_sku'));
        $virtualData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
                $attrData['option_2']['admin_option_name'];
        $configurable = $this->loadData('configurable_product_required',
                array('configurable_attribute_title' => $attrData['admin_title']),
                array('general_name', 'general_sku'));
        $configurable['associated_products_configurable_data'] =
                $this->loadData('associated_products_configurable_data',
                        array('associated_products_sku' => $virtualData['general_sku']));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $configurable['general_name'],
                      'product_sku' => $configurable['general_sku']));

        //Steps
        $this->productHelper()->createProduct($virtualData, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        echo "success_saved_produc\n";
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);


    }

    /**
     * @TODO
     */
    public function test_Configurable_Physical()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_Configurable_Combined()
    {
        // @TODO
    }

    /**
     * <p>Creating duplicated Bundle Product with Fixed type of product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function duplicateBundleFixed()
    {
        //Data
        $productData = $this->loadData('fixed_bundle_required', null, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $productData['general_name'],
                      'product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }

    /**
     * <p>Creating duplicated Bundle Product with Dynamic type of product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function test_Bundle_Dynamic()
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required', null, array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                array('product_name' => $productData['general_name'],
                      'product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
    }
}
