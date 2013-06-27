<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Add address tests.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Customer_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Verify that address is present.
     * PreConditions: Customer is opened on 'Addresses' tab.
     *
     * @param array $addressData
     *
     * @return int|mixed|string
     */
    public function isAddressPresent(array $addressData)
    {
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        $elements = $this->getControlElements('pageelement', 'list_customer_address', null, false);
        foreach ($elements as $element) {
            $element->click();
            $arrayId = explode('_', $element->attribute('id'));
            $value = end($arrayId);
            $this->addParameter('address_number', $value);
            $this->waitForControlVisible('fieldset', 'edit_address');
            if ($this->verifyForm($addressData, 'addresses')) {
                $this->clearMessages('verification');
                return $value;
            }
        }
        $this->clearMessages('verification');
        return 0;
    }

    /**
     * Defining and adding %address_number% for customer Uimap.
     * PreConditions: Customer is opened on 'Addresses' tab. New address form for filling is added
     */
    public function addAddressNumber()
    {
        $addressCount = $this->getControlCount('pageelement', 'list_customer_address');
        $this->addParameter('index', $addressCount);
        $param = $this->getControlAttribute('pageelement', 'list_customer_address_index', 'id');
        $this->addParameter('address_number', preg_replace('/(\D)+/', '', $param));
    }

    public function deleteAllAddresses($searchData)
    {
        $this->openCustomer($searchData);
        $this->openTab('addresses');
        $addressCount = $this->getControlCount('pageelement', 'list_customer_address');
        if ($addressCount > 0) {
            $this->addParameter('index', $addressCount);
            $param = $this->getControlAttribute('pageelement', 'list_customer_address_index', 'id');
            $this->addParameter('address_number', preg_replace('/[a-zA-z]+_/', '', $param));
            $this->fillRadiobutton('default_billing_address', 'Yes');
            $this->fillRadiobutton('default_shipping_address', 'Yes');
            for ($i = 1; $i <= $addressCount; $i++) {
                $this->addParameter('index', $i);
                $param = $this->getControlAttribute('pageelement', 'list_customer_address_index', 'id');
                $this->addParameter('address_number', preg_replace('/[a-zA-z]+_/', '', $param));
                $this->clickControlAndConfirm('button', 'delete_address', 'confirmation_for_delete_address', false);
            }
            $this->saveForm('save_customer');
            $this->assertMessagePresent('success');
        }
    }

    /**
     * Add address for customer.
     * PreConditions: Customer is opened.
     *
     * @param array $addressData
     */
    public function addAddress(array $addressData)
    {
        //Open 'Addresses' tab
        $this->openTab('addresses');
        $this->clickButton('add_new_address', false);
        $this->addAddressNumber();
        $this->waitForElement($this->_getControlXpath('fieldset', 'edit_address'));
        //Fill in 'Customer's Address' tab
        $this->fillTab($addressData, 'addresses');
    }

    /**
     * Create customer.
     * PreConditions: 'Manage Customers' page is opened.
     *
     * @param array $userData
     * @param array $addressData
     */
    public function createCustomer(array $userData, array $addressData = null)
    {
        //Click 'Add New Customer' button.
        $this->clickButton('add_new_customer');
        // Verify that 'send_from' field is present
        if (array_key_exists('send_from', $userData) && !$this->controlIsPresent('dropdown', 'send_from')) {
            unset($userData['send_from']);
        }
        if (array_key_exists('associate_to_website', $userData)
            && !$this->controlIsPresent('dropdown', 'associate_to_website')
        ) {
            unset($userData['associate_to_website']);
        }
        //Fill in 'Account Information' tab
        $this->fillForm($userData, 'account_information');
        //Add address
        if (isset($addressData)) {
            $this->addAddress($addressData);
        }
        $this->saveForm('save_customer');
    }

    /**
     * Open customer.
     * PreConditions: 'Manage Customers' page is opened.
     *
     * @param array $searchData
     * @param string $fieldsetName
     */
    public function openCustomer(array $searchData, $fieldsetName = 'customers_grid')
    {
        //Search Customer
        $searchData = $this->_prepareDataForSearch($searchData);
        $customerLocator = $this->search($searchData, $fieldsetName);
        $this->assertNotNull($customerLocator, 'Customer is not found with data: ' . print_r($searchData, true));
        $customerRowElement = $this->getElement($customerLocator);
        $customerUrl = $customerRowElement->attribute('title');
        //Define and add parameters for new page
        $this->addParameter('id', $this->defineIdFromUrl($customerUrl));
        //Open Customer
        $this->url($customerUrl);
        $pageUIMap = $this->getUimapPage('admin', 'edit_customer');
        $param = trim($this->getControlElement('pageelement', 'customer_header', $pageUIMap)->text());
        $this->addParameter('elementTitle', $param);
        $this->validatePage('edit_customer');
    }

    /**
     * @param array $searchData
     * @return string
     */
    public function getCustomerRegistrationDate(array $searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'customers_grid');
        $this->assertNotNull($xpathTR, 'Customer is not found');
        $cellId = $this->getColumnIdByName('Customer Since');

        return trim($this->getElement($xpathTR . '/td[' . $cellId . ']')->text());
    }

    /**
     * Register Customer on Frontend.
     * PreConditions: 'Login or Create an Account' page is opened.
     *
     * @param array $registerData
     * @param bool $disableCaptcha
     *
     * @return void
     */
    public function registerCustomer(array $registerData, $disableCaptcha = true)
    {
        $currentPage = $this->getCurrentPage();
        $this->clickButton('create_account');
        // Disable CAPTCHA if present
        if ($disableCaptcha && $this->controlIsPresent('pageelement', 'captcha')) {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('Captcha/disable_frontend_captcha');
            $this->frontend($currentPage);
            $this->clickButton('create_account');
        }
        $this->fillForm($registerData);
        $waitConditions = array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('link', 'log_out')
        );
        $this->clickButton('submit', false);
        $this->waitForElement($waitConditions);
        $this->validatePage();
    }

    /**
     * Log in customer at frontend.
     *
     * @param array $loginData
     * @param bool $validateLogin
     */
    public function frontLoginCustomer(array $loginData, $validateLogin = true)
    {
        if ($this->getArea() !== 'frontend' || !$this->controlIsVisible('link', 'log_in')) {
            $this->logoutCustomer();
        }
        $this->clickControl('link', 'log_in');
        $this->fillFieldset($loginData, 'log_in_customer');
        $waitConditions = array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('link', 'log_out')
        );
        $this->clickButton('login', false);
        $this->waitForElement($waitConditions);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->setCurrentPage($this->_findCurrentPageFromUrl());
        if ($validateLogin) {
            $this->assertTrue($this->controlIsPresent('link', 'log_out'), 'Customer is not logged in.');
        }
    }

    /**
     * Check if customer is present in customers grid
     *
     * @param array $userData
     *
     * @return bool
     */
    public function isCustomerPresentInGrid($userData)
    {
        return $this->search(array('email' => $userData['email']), 'customers_grid') !== null;
    }

    /**
     * Two Step Password Reset
     *
     * @param array $emailData
     */
    public function frontForgotPassword($emailData)
    {
        $waitCondition = array(
            $this->_getMessageXpath('general_success'),
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation')
        );
        $this->assertTrue($this->checkCurrentPage('forgot_customer_password'), $this->getParsedMessages());
        $this->fillFieldset($emailData, 'forgot_password');
        $this->clickButton('submit', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }
}