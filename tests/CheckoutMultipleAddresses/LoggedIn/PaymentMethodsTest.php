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
class CheckoutMultipleAddresses_LoggedIn_PaymentMethodsTest extends Mage_Selenium_TestCase
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
     * <p>Register Customer</p>
     *
     * @test
     * @return $userData
     */
    public function preconditionsRegisterCustomer()
    {

    }


    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>2.Customer without address is registered.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Fill in Select Addresses page.</p>
     * <p>5. Click 'Continue to Shipping Information' button.</p>
     * <p>6. Fill in Shipping Information page</p>
     * <p>7. Click 'Continue to Billing Information' button.</p>
     * <p>8. Select Payment Method(by data provider).</p>
     * <p>9. Click 'Continue to Review Your Order' button.</p>
     * <p>10. Verify information into "Place Order" page</p>
     * <p>11. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends preconditionsCreateProduct
     * @depends preconditionsRegisterCustomer
     * @dataProvider dataWithout3DSecure
     * @param $payment
     * @param $simpleSku
     * @param $userData
     * @test
     */
    public function differentPaymentMethodsWithout3D($payment, $simpleSku,$userData)
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
     * <p>2.Customer without address is registered.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Fill in Select Addresses page.</p>
     * <p>5. Click 'Continue to Shipping Information' button.</p>
     * <p>6. Fill in Shipping Information page</p>
     * <p>7. Click 'Continue to Billing Information' button.</p>
     * <p>8. Select Payment Method(by data provider).</p>
     * <p>9. Click 'Continue to Review Your Order' button.</p>
     * <p>10. Enter 3D security code.</p>
     * <p>11. Verify information into "Place Order" page</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends preconditionsCreateProduct
     * @depends preconditionsRegisterCustomer
     * @dataProvider dataWithout3DSecure
     * @param $payment
     * @param $simpleSku
     * @param $userData
     * @test
     */
    public function differentPaymentMethodsWith3D($payment, $simpleSku,$userData)
    {
        if ($payment == 'authorizenet') {
            self::$useTearDown = TRUE;
        }
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
