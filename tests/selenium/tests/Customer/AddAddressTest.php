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
        //Verifying
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        return $userData;
    }

    /**
     * @depends test_CreateCustomer
     */
    public function test_WithRequiredFieldsOnly(array $userData)
    {
        //Data
        $searchData = $this->loadData('search_customer', array('email' => $userData['email']));
        $addressData = $this->loadData('generic_address');
        //Steps
        $this->searchAndOpen($searchData);
        $this->_currentPage = 'edit_customer';
        $this->clickControl('tab', 'addresses', FALSE);
        $xpath = $this->getCurrentLocationUimapPage()->findFieldset('list_customer_addresses')->getXPath();
        $addressCount = $this->getXpathCount('//' . $xpath . '//li') + 1;
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number' => $addressCount)));
        $this->clickButton('add_new_address', FALSE);
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        return $searchData;
    }

    /**
     * Add Address for customer with one empty reqired field.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_emptyFields
     */
    public function test_WithRequiredFieldsEmpty($emptyField, array $searchData)
    {
        //Data
        $addressData = $this->loadData('generic_address', $emptyField);
        //Steps
        $this->clickButton('reset_filter'/* ,FALSE */);
//        @TODO
//        $this->pleaseWait();
        $this->searchAndOpen($searchData);
        $this->_currentPage = 'edit_customer';
        $this->clickControl('tab', 'addresses', FALSE);
        $xpath = $this->getCurrentLocationUimapPage()->findFieldset('list_customer_addresses')->getXPath();
        $addressCount = $this->getXpathCount('//' . $xpath . '//li') + 1;
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number' => $addressCount)));
        $this->clickButton('add_new_address', FALSE);
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        //Verifying
        foreach ($emptyField as $key => $value) {
            if ($this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findField($key) != Null) {
                $fieldXpath = $this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findField($key);
                $xpath1 = '//' . $this->_paramsHelper->replaceParameters($fieldXpath);
                if (!$this->isElementPresent($xpath1)) {
                    $fieldXpath = $this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findDropdown($key);
                }
            } else {
                $fieldXpath = $this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath) . "/ancestor::div[@class='multi-input']";
            } else {
                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath);
            }
            $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $fieldXpath)));
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
    }

    public function data_emptyFields()
    {
        return array(
            array(array('first_name' => '')),
            array(array('last_name' => '')),
            array(array('street_address_line_1' => '')),
            array(array('city' => '')),
            array(array('country' => '')),
            array(array('state' => '')),
            array(array('zip_code' => '')),
            array(array('telephone' => ''))
        );
    }

    /**
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues(array $searchData)
    {
        //Data
        $longValues = array(
            'default_billing_address' => 'Yes',
            'default_shipping_address' => 'yes',
            'prefix' => $this->generate('string', 255, ':alnum:'),
            'first_name' => $this->generate('string', 255, ':alnum:'),
            'middle_name_initial' => $this->generate('string', 255, ':alnum:'),
            'last_name' => $this->generate('string', 255, ':alnum:'),
            'suffix' => $this->generate('string', 255, ':alnum:'),
            'company' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_1' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_2' => $this->generate('string', 255, ':alnum:'),
            'city' => $this->generate('string', 255, ':alnum:'),
            'country' => 'Ukraine',
            'state' => $this->generate('string', 255, ':alnum:'),
            'zip_code' => $this->generate('string', 255, ':alnum:'),
            'telephone' => $this->generate('string', 255, ':alnum:'),
            'fax' => $this->generate('string', 255, ':alnum:')
        );
        $addressData = $this->loadData('all_fields_address', $longValues);
        //Steps
        $this->clickButton('reset_filter'/* ,FALSE */);
//        @TODO
//        $this->pleaseWait();
        $this->searchAndOpen($searchData);
        $this->_currentPage = 'edit_customer';
        $this->clickControl('tab', 'addresses', FALSE);
        $xpath = $this->getCurrentLocationUimapPage()->findFieldset('list_customer_addresses')->getXPath();
        $addressCount = $this->getXpathCount('//' . $xpath . '//li') + 1;
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number' => $addressCount)));
        $this->clickButton('add_new_address', FALSE);
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        //Verifying
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        // @TODO
        // check saved values
//        $this->clickButton('reset_filter'/* ,FALSE */);
//        @TODO
//        $this->pleaseWait();
//        $this->searchAndOpen($searchData);
//        $this->clickControl('tab', 'addresses', FALSE);
//        foreach ($longValues as $key => $value) {
//            if ($this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findField($key) != Null) {
//                $fieldXpath = $this->getUimapPage('admin', 'edit_customer')->getMainForm()->getTab('addresses')->getFieldset('edit_address')->findField($key);
//                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath);
//                $this->assertEquals(strlen($this->getValue('//' . $fieldXpath)), 255);
//            }
//        }
    }

//    /**
//     * @depends test_WithRequiredFieldsOnly
//     */
//    public function test_WithDefaultBillingAddress(array $searchData)
//    {
//        // @TODO
//    }
//
//    /**
//     * @depends test_WithRequiredFieldsOnly
//     */
//    public function test_WithDefaultShippingAddress(array $searchData)
//    {
//        // @TODO
//    }
}
