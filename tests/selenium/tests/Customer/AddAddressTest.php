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
     * Log in to Backend.
     *
     * Navigate to System -> Manage Customers
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin());
        $this->assertTrue($this->navigate('manage_customers'));
    }

    public function test_CreateCustomer()
    {
        //Data
        $userData = $this->loadData('generic_customer_account', null, 'email');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage(/* 'success_saved_customer' */),
                'No success message is displayed');
        return $userData;
    }

    /**
     * @depends test_CreateCustomer
     */
    public function test_WithRequiredFieldsOnly(array $userData)
    {
        //Data
        $searchData = $this->loadData('search_customer',
                        array('email' => $userData['email']), NULL);
        $addressData = $this->loadData('generic_address', null, null);
        //Steps
        $this->searchAndOpen($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address');
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        $this->assertTrue($this->successMessage('success_saved_customer'),
                'No success message is displayed');
        $this->assertFalse($this->errorMessage(), $this->messages);
        return $searchData;
    }


    /**
     * Add Address for customer with one empty reqired field.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_emptyFields
     */
    public function test_WithRequiredFieldsEmpty(array $searchData, $emptyField)
    {
        //Data
        $addressData = $this->loadData('generic_address', $emptyField, null);
        //Steps
        $this->searchAndOpen($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address');
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        foreach ($emptyField as $key => $value) {
            $xpath = $this->getCurrentLocationUimapPage()->findFieldset('edit_address')->findField($key);
        }
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
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
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues(array $searchData)
    {
        //Data
        $longValues = array(
            'default_billing_address' => 'No',
            'default_shipping_address' => 'No',
            'prefix' => $this->generate('string', 255, ':alnum:'),
            'first_name' => $this->generate('string', 255, ':alnum:'),
            'middle_name_initial' => $this->generate('string', 255, ':alnum:'),
            'last_name' => $this->generate('string', 255, ':alnum:'),
            'suffix' => $this->generate('string', 255, ':alnum:'),
            'company' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_1' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_2' => $this->generate('string', 255, ':alnum:'),
            'city' => $this->generate('string', 255, ':alnum:'),
            'zip_code' => $this->generate('string', 255, ':alnum:'),
            'telephone' => $this->generate('string', 255, ':alnum:'),
            'fax' => $this->generate('string', 255, ':alnum:')
        );
        $addressData = $this->loadData('all_fields_address', $longValues, null);
        //Steps
        $this->searchAndOpen($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address');
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_and_continue_edit');
        $this->assertTrue($this->successMessage('success_saved_customer'),
                'No success message is displayed');
        $this->assertFalse($this->errorMessage(), $this->messages);
        // @TODO
        // check saved values
    }

    /**
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithDefaultBillingAddress(array $searchData)
    {
        // @TODO
    }

    /**
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithDefaultShippingAddress(array $searchData)
    {
        // @TODO
    }
}
