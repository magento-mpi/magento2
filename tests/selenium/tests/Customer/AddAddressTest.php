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
     * This method is called before the first test of this test class is run.
     * 
     */
    protected function setUpBeforeTestRun()
    {
    }

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
        $this->assertTrue($this->admin());
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');

        // for replacing in MCA
        $this->addParameter('id', '0');
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
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
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
        $addressData = $this->loadData('generic_address');
        //Steps
        // 1.Open customer
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
        // Defining and adding %address_number% for customer Uimap
        $this->addAddressNumber();
        // 2.Add Address and save
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address', FALSE);
        $this->assertTrue($this->fillForm($addressData, 'addresses'), $this->messages);
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
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
    public function test_WithRequiredFieldsEmpty($emptyField, array $searchData)
    {
        //Data
        $addressData = $this->loadData('generic_address', $emptyField);
        //Steps
        // 1.Open customer
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
        // Defining and adding %address_number% for customer Uimap
        $this->addAddressNumber();
        // 2.Add Address and save
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address', FALSE);
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        //Verifying
        // Defining and adding %fieldXpath% for customer Uimap
        $page = $this->getUimapPage('admin', 'edit_customer');
        $fieldSet = $page->findFieldset('edit_address');
        foreach ($emptyField as $key => $value) {
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
                if (!$this->isElementPresent('//' . $this->_paramsHelper->replaceParameters($fieldXpath))) {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath)
                        . "/ancestor::div[@class='multi-input']";
            } else {
                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath);
            }
            $this->addParameter('fieldXpath', $fieldXpath);
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
    public function test_WithLongValues(array $searchData)
    {
        //Data
        $longValues = array(
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
        $addressData = $this->loadData('generic_address', $longValues);
        //Steps
        // 1.Open customer
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
        // Defining and adding %address_number% for customer Uimap
        $this->addAddressNumber();
        // 2.Add Address and save
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address', FALSE);
        $this->fillForm($addressData, 'addresses');
        $this->clickButton('save_customer');
        //Verifying в„–1
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        // 3.Open customer
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
        $this->clickControl('tab', 'addresses', FALSE);
        //Verifying в„–2 - Check saved values
        $addressNumber = $this->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
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

    /**
     * *********************************************
     * *         HELPER FUNCTIONS                  *
     * *********************************************
     */

    /**
     * Verify that address is present.
     *
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
            $page = $this->getCurrentUimapPage();
            $fieldSet = $page->findFieldset('edit_address');
            $res = 0;
            foreach ($addressData as $key => $value) {
                // Get Xpath
                if ($fieldSet->findField($key) != Null) {
                    $fieldXpath = $fieldSet->findField($key);
                    $fieldType = 'field';
                    if (!$this->isElementPresent('//' . $this->_paramsHelper->replaceParameters($fieldXpath))) {
                        $fieldXpath = $fieldSet->findDropdown($key);
                        $fieldType = 'dropdown';
                    }
                } elseif ($fieldSet->findDropdown($key) != Null) {
                    $fieldXpath = $fieldSet->findDropdown($key);
                    $fieldType = 'dropdown';
                }
                $fieldXpath = $this->_paramsHelper->replaceParameters($fieldXpath);
                // Get value
                switch ($fieldType) {
                    case 'field':
                        $valueXpath = '//' . $fieldXpath . '/@value';
                        $text = $this->getValue($valueXpath);
                        break;
                    case 'dropdown':
                        $valueXpath = '//' . $fieldXpath . '/option[@selected]';
                        $text = $this->getText($valueXpath);
                        break;
                }
                if ($value === $text) {
                    $res++;
                }
            }
            if ($res == count($addressData)) {
                return $id;
            }
        }
        return 0;
    }

    /**
     * Defining and adding %address_number% for customer Uimap
     */
    public function addAddressNumber()
    {
        $page = $this->getCurrentUimapPage();
        $fieldSet = $page->findFieldset('list_customer_addresses');
        $xpath = $fieldSet->getXPath();
        $addressCount = $this->getXpathCount('//' . $xpath . '//li') + 1;
        $this->addParameter('address_number', $addressCount);
    }

}
