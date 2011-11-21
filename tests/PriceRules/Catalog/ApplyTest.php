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
 * Catalog Price Rules applying in frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_Catalog_ApplyTest extends Mage_Selenium_TestCase
{
    protected $ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Promotions -> Catalog Price Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->addParameter('productUrl', NULL);
        $this->addParameter('categoryUrl', NULL);
        $this->loginAdminUser();
        $this->priceRulesHelper()->setAllToInactive();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     *
     * @test
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @test
     */
    public function createCategory()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();
        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Simple Products for tests</p>
     *
     * @depends createCategory
     * @test
     */
    public function createProduct($category)
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_for_price_rules_validation_front',
            array('categories' => $category), array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        return $simpleProductData;
    }

    /**
     * <p>Create catalog price rule - To Fixed Amount</p>
     *
     * <p>Steps</p>
     * <p>1. Click "Add New Rule"</p>
     * <p>2. Fill in required fields</p>
     * <p>3. Select in "General Information" -> "Customer Groups" = "NOT LOGGED IN"</p>
     * <p>3. Select in "Apply" field option - "To Fixed Amount"</p>
     * <p>4. Specify "Discount Amount" = 10%</p>
     * <p>5. Click "Save and Apply" button</p>
     * <p>Expected result: New rule created, success message appears</p>
     *
     * <p>Verification</p>
     *
     * <p>6. Open product in Frontend as a GUEST</p>
     * <p>7. Verify product special price = $10.00</p>
     * <p>8. Login to Frontend</p>
     * <p>9. Verify product REGULAR PRICE = $120.00</p>
     *
     * @dataProvider ruleTypes
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     * @test
     */

    public function applyRuleToSimpleFront($ruleType, $customerData, $categoryData, $productData)
    {
        //Data
        $nodes = explode('/', $categoryData);
        $category = end($nodes);
        $priceRuleData = $this->loadData($ruleType . '_data', array('category' => $categoryData), 'rule_name');
        $productPriceLogged = $this->loadData($ruleType . '_simple_product_logged');
        $productPriceNotLogged = $this->loadData($ruleType . '_simple_product_not_logged');
        $priceInCategoryLogged = $this->loadData($ruleType . '_simple_logged_category',
            array('product_name' => $productData['general_name'], 'category' => $category));
        $priceInCategoryNotLogged = $this->loadData($ruleType . '_simple_not_logged_category',
            array('product_name' => $productData['general_name'], 'category' => $category));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('default_tax_config');
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->saveForm('apply_rules');
        //Verification
        $this->assertTrue($this->successMessage('success_applied_rule'), $this->messages);
        $this->assertNotEquals(NULL, $this->search(array('filter_rule_name' => $priceRuleData['info']['rule_name'])));
        //Verification on frontend
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategoryAndValidateProduct($priceInCategoryNotLogged);
        $this->addParameter('categoryUrl', NULL);
        $this->productHelper()->frontOpenProduct($productData['general_name'], $categoryData);
        $this->categoryHelper()->frontVerifyProductPrices($productPriceNotLogged);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->categoryHelper()->frontOpenCategoryAndValidateProduct($priceInCategoryLogged);
        $this->addParameter('categoryUrl', NULL);
        $this->productHelper()->frontOpenProduct($productData['general_name'], $categoryData);
        $this->categoryHelper()->frontVerifyProductPrices($productPriceLogged);
        //Cleanup
        $this->loginAdminUser();
        $this->navigate('manage_catalog_price_rules');
        $this->ruleToBeDeleted = $this->loadData('search_catalog_rule',
            array('filter_rule_name' => $priceRuleData['info']['rule_name']));
    }

    public function ruleTypes()
    {
       return array(
           array('by_percentage_of_the_original_price'),
           array('by_fixed_amount'),
           array('to_percentage_of_the_original_price'),
           array('to_fixed_amount')
       );
    }

    protected function tearDown()
    {
        if (!empty($this->ruleToBeDeleted)) {
            $this->priceRulesHelper()->deleteRule($this->ruleToBeDeleted);
            $this->ruleToBeDeleted = array();
        }
    }
}
