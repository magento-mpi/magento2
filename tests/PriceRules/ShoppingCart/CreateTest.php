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
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * <p>Create Shopping cart price rule with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but leave one required field empty (empty fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one empty required field;</p>
     * <p>Expected results:</p>
     * <p>Rule is not created; Message "This is required field" is shown under each empty required field;</p>
     * 
     * @test
     */
    public function createWithEmptyRequiredFields()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with invalid data in fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but one field should be filled with incorrect data (e.g: literals in numeric fields; incorrect data is listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one field filled with incorrect data;</p>
     * <p>Expected results:</p>
     * <p>Rule is not created; Appropriate message should appear;</p>
     *
     * @test
     */
    public function createWithInvalidData()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with special symbols in fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but one field should be filled with special symbols (fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one field filled with special symbols;</p>
     * <p>Expected results:</p>
     * <p>Rule is created where applicable;</p>
     *
     * @test
     */
    public function createWithRequiredFieldsWithSpecialSymbols()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with required fields only filled.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form (only required fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithRequiredFields()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     * 
     * @test
     */
    public function createWithAllFields()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithoutCoupon()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with Percent Of Product Price Discount and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Percent Of Product Price Discount in Actions->Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting percent of each product;</p>
     *
     * @test
     */
    public function createPercentOfProductPriceDiscount()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with Fixed Amount Discount and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Fixed Amount Discount in Actions->Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting fixed amount for each product in shopping cart;</p>
     *
     * @test
     */
    public function createFixedAmountDiscount()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * <p>Create Shopping cart price rule with Fixed Amount Discount For Whole Cart and coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions->Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR (Fixed Amount Discount For Whole Cart in Actions->Apply section); Select specific category in conditions; Add coupon that should be applied;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>4. Navigate to frontend;</p>
     * <p>5. Add product(s) for which rule should be applied to shopping cart;</p>
     * <p>6. Apply coupon for the shopping cart;</p>
     * <p>6. Verify prices for the product(s) in the totals of shopping cart;</p>
     * <p>Expected results:</p>
     * <p>Rule is created; Totals changed after applying coupon; Rule is discounting fixed amount for whole cart;</p>
     *
     * @test
     */
    public function createFixedAmountDiscountForWholeCart()
    {
        $this->markTestSkipped('TODO');
    }
}
