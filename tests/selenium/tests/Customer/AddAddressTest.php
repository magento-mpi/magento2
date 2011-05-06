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
 * Add address tests.
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
        $this->loginAdminUser();
        $this->assertTrue($this->checkCurrentPage('dashboard'),
                'Wrong page is opened');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'Wrong page is opened');
    }

    /**
     * Create customer for add customer address tests
     *
     * @return array
     */
    public function test_CreateCustomer()
    {
        //Data
        $userData = $this->loadData('generic_customer_account',
                        array('email' => $this->generate('email', 20, 'valid')));
        //Steps
        $this->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        return $userData;
    }

    /**
     * Add address for customer. Fill in only required field.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in required fields.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address is added. Customer info is saved.
     *
     * Success Message is displayed
     *
     * @depends test_CreateCustomer
     *
     * @param array $userData
     * @return array
     */
    public function test_WithRequiredFieldsOnly(array $userData)
    {
        //Data
        $searchData = $this->loadData('search_customer', array('email' => $userData['email']));
        $addressData = $this->loadData('generic_address_with_state');
        //Steps
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        return $searchData;
    }

    /**
     * Add Address for customer with one empty reqired field.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in fields exept one required.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address isn't added. Customer info is not saved.
     *
     * Error Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_emptyFields
     *
     * @param array $emptyField
     * @param array $searchData
     */
    public function test_WithRequiredFieldsEmpty($emptyField, $searchData)
    {
        //Data
        $addressData = $this->loadData('generic_address_with_state', $emptyField);
        //Steps
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        // Defining and adding %fieldXpath% for customer Uimap
        $page = $this->getUimapPage('admin', 'edit_customer');
        $page->assignParams($this->_paramsHelper);
        $fieldSet = $page->findFieldset('edit_address');
        foreach ($emptyField as $key => $value) {
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
                if (!$this->isElementPresent('//' . $fieldXpath)) {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath .= "/ancestor::div[@class='multi-input']";
            }
            $this->addParameter('fieldXpath', $fieldXpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
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
     * Add address for customer. Fill in all fields by using special characters(except the field "country").
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in fields by long value alpha-numeric data exept 'country' field.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address is added. Customer info is saved.
     *
     * Success Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_ExeptCountry(array $searchData)
    {
        //Data
        $specialCharacters = array(
            'prefix' => $this->generate('string', 32, ':punct:'),
            'first_name' => $this->generate('string', 32, ':punct:'),
            'middle_name' => $this->generate('string', 32, ':punct:'),
            'last_name' => $this->generate('string', 32, ':punct:'),
            'suffix' => $this->generate('string', 32, ':punct:'),
            'company' => $this->generate('string', 32, ':punct:'),
            'street_address_line_1' => $this->generate('string', 32, ':punct:'),
            'street_address_line_2' => $this->generate('string', 32, ':punct:'),
            'city' => $this->generate('string', 32, ':punct:'),
            'region' => $this->generate('string', 32, ':punct:'),
            'zip_code' => $this->generate('string', 32, ':punct:'),
            'telephone' => $this->generate('string', 32, ':punct:'),
            'fax' => $this->generate('string', 32, ':punct:')
        );
        $addressData = $this->loadData('generic_address_with_region', $specialCharacters);
        //Steps
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying #–1
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->openCustomer($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        //Verifying #–2 - Check saved values
        $addressNumber = $this->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * Add address for customer. Fill in only required field. Use max long values for fields.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in fields by long value alpha-numeric data exept 'country' field.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address is added. Customer info is saved.
     *
     * Success Message is displayed. Length of fields are 255 characters.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues_ExeptCountry(array $searchData)
    {
        //Data
        $longValues = array(
            'prefix' => $this->generate('string', 255, ':alnum:'),
            'first_name' => $this->generate('string', 255, ':alnum:'),
            'middle_name' => $this->generate('string', 255, ':alnum:'),
            'last_name' => $this->generate('string', 255, ':alnum:'),
            'suffix' => $this->generate('string', 255, ':alnum:'),
            'company' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_1' => $this->generate('string', 255, ':alnum:'),
            'street_address_line_2' => $this->generate('string', 255, ':alnum:'),
            'city' => $this->generate('string', 255, ':alnum:'),
            'region' => $this->generate('string', 255, ':alnum:'),
            'zip_code' => $this->generate('string', 255, ':alnum:'),
            'telephone' => $this->generate('string', 255, ':alnum:'),
            'fax' => $this->generate('string', 255, ':alnum:')
        );
        $addressData = $this->loadData('generic_address_with_region', $longValues);
        //Steps
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying #–1
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->openCustomer($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        //Verifying #–2 - Check saved values
        $addressNumber = $this->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * Add address for customer. Fill in only required field. Use this address as Default Billing.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in required fields.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address is added. Customer info is saved.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithDefaultBillingAddress(array $searchData)
    {
        //Data
        $addressData = $this->loadData('all_fields_address_with_state',
                        array('default_billing_address' => 'Yes'));
        //Steps
        // 1.Open customer
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->openCustomer($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        //Verifying #–2 - Check saved values
        $addressNumber = $this->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * Add address for customer. Fill in only required field. Use this address as Default Shipping.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Open 'Addresses' tab.
     *
     * 3. Click 'Add New Address' button.
     *
     * 4. Fill in required fields.
     *
     * 5. Click  'Save Customer' button
     *
     * Expected result:
     *
     * Customer address is added. Customer info is saved.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithDefaultShippingAddress(array $searchData)
    {
        $addressData = $this->loadData('all_fields_address_with_region',
                        array('default_shipping_address' => 'Yes'));
        //Steps
        $this->openCustomer($searchData);
        $this->addAdress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->openCustomer($searchData);
        $this->clickControl('tab', 'addresses', FALSE);
        //Verifying #–2 - Check saved values
        $addressNumber = $this->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * *********************************************
     * *         HELPER FUNCTIONS                  *
     * *********************************************
     */

    /**
     * Verify that address is present.
     *
     * PreConditions: Customer is opened on 'Addresses' tab.
     * @param array $addressData
     */
    public function isAddressPresent(array $addressData)
    {
        $page = $this->getCurrentUimapPage();
        $fieldSet = $page->findFieldset('list_customer_addresses');
        $xpath = '//' . $fieldSet->getXPath() . '//li';
        $addressCount = $this->getXpathCount($xpath);
        for ($i = 1; $i <= $addressCount; $i++) {
            $this->click($xpath . "[$i]");
            $id = $this->getValue($xpath . "[$i]/@id");
            $arrayId = explode('_', $id);
            $id = end($arrayId);
            $this->addParameter('address_number', $id);
            $page->assignParams($this->_paramsHelper);
            if ($this->verifyForm($addressData, 'addresses')) {
                return $id;
            }
//            $page = $this->getCurrentUimapPage();
//            $page->assignParams($this->_paramsHelper);
//            $tab = $page->findTab('addresses');
//            $res = 0;
//            foreach ($addressData as $key => $value) {
//                // Get Xpath
//                if ($tab->findField($key) != Null) {
//                    $fieldXpath = $tab->findField($key);
//                    $fieldType = 'field';
//                    if (!$this->isElementPresent('//' . $fieldXpath)) {
//                        echo "----------------------$key\n";
//                        $fieldXpath = $tab->findDropdown('state');
//                        $fieldType = 'dropdown';
//                    }
//                } elseif ($tab->findDropdown($key) != Null) {
//                    $fieldXpath = $tab->findDropdown($key);
//                    $fieldType = 'dropdown';
//                } elseif ($tab->findRadiobutton($key) != Null) {
//                    $fieldXpath = $tab->findRadiobutton($key);
//                    $fieldType = 'radiobutton';
//                }
//                // Get value
//                switch ($fieldType) {
//                    case 'field':
//                        $valueXpath = '//' . $fieldXpath;
//                        $text = $this->getValue($valueXpath);
//                        if ($value === $text) {
//                            $res++;
//                        }
//                        break;
//                    case 'dropdown':
//                        $valueXpath = '//' . $fieldXpath;
////                        $valueXpath = '//' . $fieldXpath . '/option[@selected]';
//                        $text = $this->getSelectedLabels($valueXpath);
//                        if ($value === $text) {
//                            $res++;
//                        }
//                        break;
//                    case 'radiobutton':
//                        $valueXpath = '//' . $fieldXpath;
//                        $f_val = $this->getValue($valueXpath);
//                        echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-------$f_val---------\n";
//                        if (($f_val == 'on' && strtolower($value) == 'yes') || ($f_val == 'off' && (strtolower($value) == 'no' || $value == ''))) {
//                            $res++;
//                        }
//                }
//            }
//            if ($res == count($addressData)) {
//                echo "$id\n";
//                return $id;
//            }
        }
        return 0;
    }

    /**
     * Defining and adding %address_number% for customer Uimap.
     *
     * PreConditions: Customer is opened on 'Addresses' tab.
     */
    public function addAddressNumber()
    {
        $page = $this->getCurrentUimapPage();
        $fieldSet = $page->findFieldset('list_customer_addresses');
        $xpath = $fieldSet->getXPath();
        $addressCount = $this->getXpathCount('//' . $xpath . '//li') + 1;
        $this->addParameter('address_number', $addressCount);
        $page->assignParams($this->_paramsHelper);
    }

    /**
     * Add address for customer.
     *
     * PreConditions: Customer is opened.
     * @param array $addressData
     */
    public function addAdress(array $addressData)
    {
        //Open 'Addresses' tab
        $this->clickControl('tab', 'addresses', FALSE);
        $this->addAddressNumber();
        $this->clickButton('add_new_address', FALSE);
        //Fill in 'Customer's Address' tab
        $this->fillForm($addressData, 'addresses');
    }

    /**
     * Create customer.
     *
     * PreConditions: 'Manage Customers' page is opened.
     * @param array $userData
     * @param array $addressData
     */
    public function createCustomer(array $userData, array $addressData = NULL)
    {
        //Click 'Add New Customer' button.
        $this->clickButton('add_new_customer');
        // Verify that 'send_from' field is present
        if (array_key_exists('send_from', $userData)) {
            $page = $this->getCurrentUimapPage();
            $tab = $page->findTab('account_information');
            $xpath = $tab->findDropdown('send_from');
            if (!$this->isElementPresent('//' . $xpath)) {
                unset($userData['send_from']);
            }
        }
        //Fill in 'Account Information' tab
        $this->fillForm($userData, 'account_information');
        //Add address
        if (isset($addressData)) {
            $this->addAdress($addressData);
        }
        $this->saveForm('save_customer');
    }

    /**
     * Open customer.
     *
     * PreConditions: 'Manage Customers' page is opened.
     * @param array $searchData
     */
    public function openCustomer(array $searchData)
    {
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
    }

}
