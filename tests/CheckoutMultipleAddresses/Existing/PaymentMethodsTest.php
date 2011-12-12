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
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_Existing_PaymentMethodsTest extends Mage_Selenium_TestCase
{

    protected static $useTearDown = false;

    protected function assertPreConditions()
    {

    }

    /**
     * <p>Creating Simple product</p>
     *
     * @test
     * @return $simpleSku
     */
    public function preconditionsCreateProduct()
    {

    }

    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click 'Continue to Shipping Information' button.</p>
     * <p>7. Fill in Shipping Information page</p>
     * <p>8. Click 'Continue to Billing Information' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue to Review Your Order' button.</p>
     * <p>11. Verify information into "Place Order" page</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param $payment
     * @param $simpleSku
     * @depends preconditionsCreateProduct
     * @dataProvider dataWithout3DSecure
     * @test
     */
    public function differentPaymentMethodsWithout3D($payment, $simpleSku)
    {

    }

    public function dataWithout3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    /**
     * <p>Payment methods with 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click 'Continue to Shipping Information' button.</p>
     * <p>7. Fill in Shipping Information page</p>
     * <p>8. Click 'Continue to Billing Information' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue to Review Your Order' button.</p>
     * <p>11. Enter 3D security code.</p>
     * <p>12. Verify information into "Place Order" page</p>
     * <p>13. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param $payment
     * @param $simpleSku
     * @depends preconditionsCreateProduct
     * @dataProvider dataWith3DSecure
     * @test
     */
    public function differentPaymentMethodsWith3D($payment, $simpleSku)
    {
        if ($payment == 'authorizenet') {
            self::$useTearDown = TRUE;
        }
        //Test here
    }

    public function dataWith3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    protected function tearDown()
    {
        if (!empty(self::$useTearDown)) {
            $this->loginAdminUser();
            $this->systemConfigurationHelper()->useHttps('frontend', 'no');
        }
    }
    
}
