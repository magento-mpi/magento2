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
class Customer_Account_AddAddressTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * 1. Log in to Backend.
     *
     * 2. Navigate to System -> Manage Customers
     *
     * 3. Create and open customer without address
     */
    protected function assertPreConditions()
    {
        $userData = $this->loadData('generic_customer_account', null, 'email');
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin());
        $this->assertTrue($this->navigate('manage_customers'));
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        $this->assertTrue($this->successMessage('success_save_customer'),
                'No success message is displayed');
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
    }

    /**
     * Add Address for customer. Fill in only required fields.
     *
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->clickButton('add_new_address');
        $this->fillForm($this->loadData('generic_address', null, null));
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_customers'),
                'After creating customer admin should be redirected to manage customers page');
        $this->assertTrue($this->successMessage('success_save_customer'),
                'No success message is displayed');
    }

    /**
     * Add Address for customer. With empty reqired fields
     *
     * @dataProvider data_emptyFields
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        $this->clickButton('add_new_address');
        $this->fillForm($this->loadData('generic_address', $emptyField, null));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_emptyFields()
    {
        return array(
            array('first_name' => ''),
            array('last_name' => ''),
            array('street_address_line_1' => NULL),
            array('city' => Null),
            array('country' => ''),
            array('state' => ''),
            array('zip_code' => Null),
            array('telephone' => Null)
        );
    }

    /**
     * @TODO
     */
    public function test_WithLongValues()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithDefaultBillingAddress()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithDefaultShippingAddress()
    {
        // @TODO
    }

}
