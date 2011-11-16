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
class PriceRules_ShoppingCart_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {}

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog - Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '0');
    }

    /**
     * Create Customer for tests
     *
     * @test
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        $addressData = $this->loadData('customer_account_address_for_prices_validation');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $customer = array('email' => $userData['email'], 'password' => $userData['password']);
        return $customer;
    }

    /**
     * Create category
     *
     * @test
     */
    public function createCategory()
    {
        $this->navigate('manage_categories');
        //Data
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        //Steps
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * Create Simple Products for tests
     *
     * @depends createCategory
     * @test
     */
    public function createProducts($category)
    {
        $products = array();
        $this->navigate('manage_products');
        for ($i = 1; $i <= 3; $i++) {
            $simpleProductData = $this->loadData('simple_product_for_prices_validation_front_' . $i,
                    array('categories' => $category), array('general_name', 'general_sku'));
            $products['sku'][$i] = $simpleProductData['general_sku'];
            $products['name'][$i] = $simpleProductData['general_name'];
            $this->productHelper()->createProduct($simpleProductData);
            $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        }
        return $products;
    }

    /**
     * <p>Create Shopping cart price rule with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but leave one required field empty (empty fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one empty required field;</p>
     * <p>Expected results:</p>
     * <p>Rule is not created; Message "This is required field" is shown under each empty required field;</p>
     *
     * @dataProvider dataEmptyFields
     * @test
     */
    public function createWithEmptyRequiredFields($fieldName, $fieldType)
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $dataToOverride = array();
        if ($fieldType == 'multiselect') {
            $dataToOverride[$fieldName] = '%noValue%';
        } else {
            $dataToOverride[$fieldName] = '';
        }
        $ruleData = $this->loadData('scpr_required_fields', $dataToOverride, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->addFieldIdToMessage($fieldType, $fieldName);
        if ($fieldName == 'discount_amount') {
            $this->assertTrue($this->validationMessage('enter_valid_number'), $this->messages);
        } else {
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyFields()
    {
        return array(
            array('rule_name', 'field'),
            array('customer_groups', 'multiselect'),
            array('coupon_code', 'field'),
            array('discount_amount', 'field')
        );
    }

    /**
     * <p>Create Shopping cart price rule with special symbols in fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but one field should be filled with special symbols (fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one field filled with special symbols;</p>
     * <p>Expected results:</p>
     * <p>Rule is created where applicable;</p>
     *
     * @test
     */
    public function createWithRequiredFieldsWithSpecialSymbols()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_required_fields',
                array(
                    'rule_name'              => $this->generate('string', 32, ':punct:'),
                    'coupon_code'            => $this->generate('string', 32, ':punct:')
                ));
        $ruleSearch = $this->loadData('search_shopping_cart_rule',
                                      array('filter_rule_name' => $ruleData['info']['rule_name'],
                                            'filter_coupon_code' => $ruleData['info']['coupon_code']));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->PriceRulesHelper()->openRule($ruleSearch);
        unset($ruleData['info']['websites']);
        $this->PriceRulesHelper()->verifyRuleData($ruleData);
    }

    /**
     * <p>Create Shopping cart price rule with required fields only filled.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (only required fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithRequiredFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_required_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);

    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithAllFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithoutCoupon()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields',
                                    array('coupon'          => 'No Coupon',
                                          'coupon_code'     => '%noValue%',
                                          'uses_per_coupon' => '%noValue%'),
                                    array('rule_name'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }

    /**
     * <p>Create Shopping cart price rule with existing coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * <p>4. Create rule with the same coupon;</p>
     * <p>Expected Results:</p>
     * <p>Rule is not created; Messsage "Coupon with the same code already exists." appears.</p>
     *
     * @test
     */
    public function createWithExistingCoupon()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->errorMessage('error_coupon_code_exists'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }

    /**
     * <p>Create Shopping cart price rule with Percent Of Product Price Discount and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Percent Of Product Price Discount in Actions - Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting percent of each product;</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProducts
     *
     * @test
     */
    public function createPercentOfProductPriceDiscount($customer, $category, $products)
    {
        $cartProductsData = $this->loadData('prices_for_percent_of_product_price_discount');
        $checkoutData = $this->loadData('totals_for_percent_of_product_price_discount');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_percent_of_product_price_discount',
                                    array('category' => $category),
                                    array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        foreach ($products['name'] as $key => $productName) {
            $cartProductsData['product_' . $key]['product_name'] = $productName;
            $this->productHelper()->frontOpenProduct($productName);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->shoppingCartHelper()->frontEstimateShipping('estimate_shipping', 'shipping_flatrate');
        $this->addParameter('couponCode', $ruleData['info']['coupon_code']);
        $this->fillForm(array('coupon_code' => $ruleData['info']['coupon_code']));
        $this->clickButton('apply_coupon');
        $this->assertTrue($this->successMessage('success_applied_coupon'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($cartProductsData, $checkoutData);
    }

    /**
     * <p>Create Shopping cart price rule with Fixed Amount Discount and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Fixed Amount Discount in Actions - Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting fixed amount for each product in shopping cart;</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProducts
     *
     * @test
     */
    public function createFixedAmountDiscount($customer, $category, $products)
    {
        $cartProductsData = $this->loadData('prices_for_fixed_amount_discount');
        $checkoutData = $this->loadData('totals_for_fixed_amount_discount');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_fixed_amount_discount',
                                    array('category' => $category),
                                    array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        foreach ($products['name'] as $key => $productName) {
            $cartProductsData['product_' . $key]['product_name'] = $productName;
            $this->productHelper()->frontOpenProduct($productName);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->shoppingCartHelper()->frontEstimateShipping('estimate_shipping', 'shipping_flatrate');
        $this->addParameter('couponCode', $ruleData['info']['coupon_code']);
        $this->fillForm(array('coupon_code' => $ruleData['info']['coupon_code']));
        $this->clickButton('apply_coupon');
        $this->assertTrue($this->successMessage('success_applied_coupon'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($cartProductsData, $checkoutData);
    }

    /**
     * <p>Create Shopping cart price rule with Fixed Amount Discount For Whole Cart and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Fixed Amount Discount For Whole Cart in Actions - Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting fixed amount for whole cart;</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProducts
     *
     * @test
     */
    public function createFixedAmountDiscountForWholeCart($customer, $category, $products)
    {
        $cartProductsData = $this->loadData('prices_for_fixed_amount_discount_for_whole_cart');
        $checkoutData = $this->loadData('totals_for_fixed_amount_discount_for_whole_cart');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_fixed_amount_discount_for_whole_cart',
                                    array('category' => $category),
                                    array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        foreach ($products['name'] as $key => $productName) {
            $cartProductsData['product_' . $key]['product_name'] = $productName;
            $this->productHelper()->frontOpenProduct($productName);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->shoppingCartHelper()->frontEstimateShipping('estimate_shipping', 'shipping_flatrate');
        $this->addParameter('couponCode', $ruleData['info']['coupon_code']);
        $this->fillForm(array('coupon_code' => $ruleData['info']['coupon_code']));
        $this->clickButton('apply_coupon');
        $this->assertTrue($this->successMessage('success_applied_coupon'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($cartProductsData, $checkoutData);
    }
}
