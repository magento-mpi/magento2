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
class Core_Mage_Customer_Helper extends Mage_Selenium_TestCase
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
        $xpath = $this->_getControlXpath('fieldset', 'list_customer_addresses') . '//li';
        $addressCount = $this->getXpathCount($xpath);
        for ($i = $addressCount; $i > 0; $i--) {
            $this->click($xpath . "[$i]");
            $id = $this->getValue($xpath . "[$i]/@id");
            $arrayId = explode('_', $id);
            $id = end($arrayId);
            $this->addParameter('address_number', $id);
            if ($this->verifyForm($addressData, 'addresses')) {
                return $id;
            }
        }
        return 0;
    }

    /**
     * Defining and adding %address_number% for customer Uimap.
     * PreConditions: Customer is opened on 'Addresses' tab. New address form for filling is added
     */
    public function addAddressNumber()
    {
        $xpath = $this->_getControlXpath('fieldset', 'list_customer_addresses');
        $addressCount = $this->getXpathCount($xpath . '//li');
        $param = preg_replace('/(\D)+/', '', $this->getAttribute($xpath . "//li[$addressCount]@id"));
        $this->addParameter('address_number', $param);
    }

    public function deleteAllAddresses($searchData)
    {
        $this->openCustomer($searchData);
        $this->openTab('addresses');
        $xpath = $this->_getControlXpath('fieldset', 'list_customer_addresses') . '//li';
        $addressCount = $this->getXpathCount($xpath);
        if ($addressCount > 0) {
            $param = preg_replace('/[a-zA-z]+_/', '', $this->getAttribute($xpath . "[$addressCount]@id"));
            $this->addParameter('address_number', $param);
            $this->fillRadiobutton('default_billing_address', 'Yes');
            $this->fillRadiobutton('default_shipping_address', 'Yes');
            for ($i = 1; $i <= $addressCount; $i++) {
                $param = preg_replace('/[a-zA-z]+_/', '', $this->getAttribute($xpath . "[$i]@id"));
                $this->addParameter('address_number', $param);
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
        if (array_key_exists('send_from', $userData)) {
            $page = $this->getCurrentUimapPage();
            $tab = $page->findTab('account_information');
            $pattern = preg_quote(' and not(@disabled)');
            $xpath = preg_replace("/$pattern/", '', $tab->findDropdown('send_from'));
            if (!$this->isElementPresent($xpath)) {
                unset($userData['send_from']);
            }
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
     */
    public function openCustomer(array $searchData)
    {
        $this->searchAndOpen($searchData, true, 'customers_grid');
    }

    /**
     * Register Customer on Frontend.
     * PreConditions: 'Login or Create an Account' page is opened.
     *
     * @param array $registerData
     */
    public function registerCustomer(array $registerData)
    {
        $currentPage = $this->getCurrentPage();
        $this->clickButton('create_account');
        // Disable CAPTCHA if present
        if ($this->controlIsPresent('pageelement', 'captcha')) {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('disable_customer_captcha');
            $this->frontend($currentPage);
            $this->clickButton('create_account');
        }
        $this->fillForm($registerData);
        $this->saveForm('submit');
    }

    /**
     * Log in customer at frontend.
     *
     * @param array $loginData
     */
    public function frontLoginCustomer(array $loginData, $strictLogin = true)
    {
        $this->frontend();
        $this->navigate('customer_account', false);
        $this->validatePage();
        if ($this->getCurrentPage() == 'customer_account') {
            $this->clickControl('link', 'log_out', false);
            $this->waitForTextPresent('You are now logged out');
            $this->waitForTextNotPresent('You are now logged out');
            $this->deleteAllVisibleCookies();
            $this->validatePage('home_page');
        }
        $this->clickControl('link', 'log_in');
        $this->fillFieldset($loginData, 'log_in_customer');
        $this->clickButton('login', false);
        if ($strictLogin) {
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage('customer_account');
        }
    }
}