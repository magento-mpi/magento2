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
class Core_Mage_ValidationVatNumber_FrontEndOrderCreation_OrderWithRegistrationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('VatID/store_information_data');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        if (!$this->controlIsVisible('pageelement', 'vat_number_is_valid')){
            $this->skipTestWithScreenshot('VAT Number is not valid');
        }
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/create_new_account_options_disable');
        $this->systemConfigurationHelper()->configure('ShippingSettings/store_information_empty');
    }

    /**
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array(
            'group_valid_vat_intraunion' => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_domestic' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat' => 'Invalid VAT_%randomize%',
            'group_default' => 'Default Group_%randomize%'
        );
        $groupNames = array();
        //Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $group = $this->loadDataSet('CustomerGroup', 'new_customer_group', array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($group);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $groupNames[$groupKey] = $group['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $groupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);
        //Data for creating product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();
        return array('name' => $simple['general_name'], 'groups' => $groupNames);
    }

    /**
     * <p>Checkout with simple product. Without VAT Number</p>
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
     */
    public function orderWithRegistration($vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $vatNumber = array_merge($vatNumber, array('general_name' => $vatGroup['name']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_usa', $vatNumber);
        //Steps
        $this->logoutCustomer();
        //Verification
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Steps. Verification Customer group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        //Verification
        $this->customerHelper()->openCustomer(array('email' => $checkoutData['billing_address_data']['billing_email']));
        $this->openTab('account_information');
        $this->verifyForm(array('group' => $vatGroup['groups'][$customerGroup]), 'account_information');
        $this->assertEmptyVerificationErrors();
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