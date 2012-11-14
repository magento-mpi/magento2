<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ValidationVatNumber_FrontEndOrderCreation_OrderForRegisteredTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/store_information_data');
        $this->systemConfigurationHelper()->expandFieldSet('store_information');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->waitForElementVisible($this->_getControlXpath('button', 'vat_number_is_valid'));
        //Verification
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_valid'), 'VAT Number is not valid');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/create_new_account_options_disable');
    }

    /**
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array('group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
                       'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
                       'group_invalid_vat'          => 'Invalid VAT_%randomize%',
                       'group_default'              => 'Default Group_%randomize%');
        $processedGroupNames = array();
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps. Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $group = $this->loadDataSet('CustomerGroup', 'new_customer_group', array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($group);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $group['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $processedGroupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);
        //Steps. Creating simple product
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple' => $simple['general_name'], 'customerGroups' => $processedGroupNames);
    }

    /**
     * <p>Checkout with simple product. Without VAT Number</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Register new customer</p>
     * <p>2. Open product page.</p>
     * <p>3. Add product to Shopping Cart.</p>
     * <p>4. Click "Proceed to Checkout".</p>
     * <p>5. Fill in Billing Information tab. Field VAT Number is empty</p>
     * <p>6. Select "Ship to this address" option.</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>8. Select Shipping Method and Payment Method.</p>
     * <p>9. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful. Customer should be assigned to Default Group</p>
     *
     * @param array $vatGroup
     * @param array $vatNumber
     * @param array|string $customerGroup
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider dataForCustomersDataProvider
     *
     * @TestlinkId TL-MAGE-3942
     * @author andrey.vergeles
     */
    public function orderForRegisteredCustomers($vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $vatNumber = array_merge($vatNumber,
            array('general_name' => $vatGroup['simple'], 'email_address' => $userData['email']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney_usa', $vatNumber);
        $userDataParam = $userData['first_name'] . ' ' . $userData['last_name'];
        //Steps
        $this->frontend();
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        //Verification
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Steps. Verification Customer group on back-end
        if (array_key_exists('billing_vat_number', $vatNumber)) {
            //Steps. Opening customer for changing group
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->addParameter('customer_first_last_name', $userDataParam);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->openTab('account_information');
        } else {
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->addParameter('customer_first_last_name', $userDataParam);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->openTab('account_information');
        }
        //Verification
        $verificationData = $vatGroup['customerGroups'];
        $this->verifyForm(array('group'=> $verificationData[$customerGroup]), 'account_information');
    }

    public function dataForCustomersDataProvider()
    {
        return array(
            array(array(), 'group_default'),
            array(array('billing_vat_number' => '111607872'), 'group_valid_vat_domestic'),
            array(array('billing_vat_number' => '1111111111'), 'group_invalid_vat'),
            array(array('billing_vat_number' => '37441119989', 'billing_country' => 'France', 'billing_state' => 'Ain'),
                  'group_valid_vat_intraunion')
        );
    }
}