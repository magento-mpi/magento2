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
 * Prices Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxAndPricesValidationBackendTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

//    /**
//     * Create Customer for tests
//     *
//     * @test
//     */
//    public function createCustomer()
//    {
//        //Preconditions
//        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
//        //Steps
//        $this->loginAdminUser();
//        $this->navigate('manage_customers');
//        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
//        $this->CustomerHelper()->createCustomer($userData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
//
//        return $userData['email'];
//    }

    /**
     * Create Category for tests
     *
     * @test
     */
    public function createCategory()
    {
        //Data
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_for_prices_validation', NULL, 'name');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * Create Simple Product for tests
     *
     * @depends createCategory
     * @test
     */
    public function createSimpleProduct($category)
    {
        //Data
        $simpleProductData = $this->loadData('simple_product_for_prices_validation',
                array('categories' => $category), array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($simpleProductData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simpleProductData;
    }

    /**
     * Create Virtual Product for tests
     *
     * @depends createCategory
     * @test
     */
    public function createVirtualProduct($category)
    {
        //Data
        $virtualProductData = $this->loadData('virtual_product_for_prices_validation',
                array('categories' => $category), array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($virtualProductData, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $virtualProductData;
    }

    /**
     * Verify product prices in the category
     *
     * @depends createCategory
     * @depends createSimpleProduct
     * @depends createVirtualProduct
     * @test
     */
    public function createOrderBackend($category, $simpleProductData, $virtualProductData)
    {
        //Data
        $nodes = explode('/', $category);
        print_r($nodes);
        $verifyProduct1 = $this->loadData('validate_product_prices_in_category_simple',
                array('product_name' => $simpleProductData['general_name'], 'category' => $nodes[1]));
        $verifyProduct2 = $this->loadData('validate_product_prices_in_category_virtual',
                array('product_name' => $virtualProductData['general_name'], 'category' => $nodes[1]));
        //Steps
        $this->logoutCustomer();
        $this->assertTrue($this->checkCurrentPage('home'), $this->messages);
        $this->categoryHelper()->frontValidateProductInCategory($verifyProduct1);
        $this->categoryHelper()->frontValidateProductInCategory($verifyProduct2);
    }


}
