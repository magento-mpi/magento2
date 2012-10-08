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
class Community2_Mage_ValidationVatNumber_FrontEndOrderCreation_OrderWithRegistrationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data');
        //Filling "Store Information" data and Validation VAT Number
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($storeInfo);
        $xpath = $this->_getControlXpath('link','store_information_link');
        if (!$this->isElementPresent($xpath . "[@class='open']")) {
            $this->clickControl('link','store_information_link', false);
        }
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_valid'), 'VAT Number is not valid');
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
            'group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat'          => 'Invalid VAT_%randomize%',
            'group_default'              => 'Default Group_%randomize%');
        $processedGroupNames = array();
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps. Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $customerGroup = $this->loadDataSet('CustomerGroup', 'new_customer_group',
                array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($customerGroup);
        //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $customerGroup['group_name'];
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

        return array('simple'         => $simple['general_name'],
                     'customerGroups' => $processedGroupNames);
    }

    protected function tearDownAfterTestClass()
    {
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options_disable');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($accountOptions);
    }

    /**
     * <p>Checkout with simple product. Without VAT Number</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>4. Fill in Billing Information tab. Field VAT Number is empty</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method and Payment Method.</p>
     * <p>8. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.Customer is registered. Customer should be assigned to Default Group</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3891
     * @author andrey.vergeles
     */
    public function customerWithoutVatNumber($data)
    {
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney',
            array('general_name' => $data['simple']));
        $userDataParam = $checkoutData['billing_address_data']['billing_first_name'] . ' ' .
                         $checkoutData['billing_address_data']['billing_last_name'];
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        //Steps. Verification Customer group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $checkoutData['billing_address_data']['billing_email']));
        $this->openTab('account_information');
        //Verification
        $this->verifyForm(array('group' => $data['customerGroups']['group_default']),'account_information');
    }

    /**
     * <p>Checkout with simple product. With valid VAT Number for domestic country</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Fill  Billing Information tab. Enter the same country as country of your store.</p>
     * <p>6. Enter valid VAT Number</p>
     * <p>7. Select "Ship to this address" option.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Shipping Method and Payment Method.</p>
     * <p>10. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.Customer is registered.</p>
     * <p> Customer should be assigned to group which was specified as "Group for Valid VAT ID - Domestic"</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3893
     * @author andrey.vergeles
     */
    public function customerWithValidVatDomestic($data)
    {
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney',
            array('general_name'       => $data['simple'],
                  'billing_vat_number' => '111607872'
            ));
        $userDataParam = $checkoutData['billing_address_data']['billing_first_name'] . ' ' .
                         $checkoutData['billing_address_data']['billing_last_name'];
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        //Steps. Verification Customer group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $checkoutData['billing_address_data']['billing_email']));
        $this->openTab('account_information');
        //Verification
        $this->verifyForm(array('group' => $data['customerGroups']['group_valid_vat_domestic']),'account_information');
    }

    /**
     * <p>Checkout with simple product. With Invalid VAT Number</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>4. Fill in Billing Information tab. Enter invalid VAT Number</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method and Payment Method.</p>
     * <p>8. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful. Customer is registered. Customer should be assigned to "Group for Invalid VAT ID"</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3899
     * @author andrey.vergeles
     */
    public function customerWithInvalidVat($data)
    {
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney',
            array('general_name'       => $data['simple'],
                  'billing_vat_number' => '1111111111'
            ));
        $userDataParam = $checkoutData['billing_address_data']['billing_first_name'] . ' ' .
                         $checkoutData['billing_address_data']['billing_last_name'];
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        //Steps. Verification Customer group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $checkoutData['billing_address_data']['billing_email']));
        $this->openTab('account_information');
        //Verification
        $this->verifyForm(array('group' => $data['customerGroups']['group_invalid_vat']),'account_information');
    }

    /**
     * <p>Checkout with simple product. With invalid VAT Number for domestic country</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Fill  Billing Information tab. Select country from Europe Union (but not the same as store country)</p>
     * <p>6. Enter valid VAT Number</p>
     * <p>7. Select "Ship to this address" option.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Shipping Method and Payment Method.</p>
     * <p>10. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.Customer is registered.</p>
     * <p>Customer should be assigned to group which was specified as "Group for Valid VAT ID - Intra-Union"</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3897
     * @author andrey.vergeles
     */
    public function customerWithValidVatIntraUnion($data)
    {
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney',
            array('general_name'       => $data['simple'],
                  'billing_vat_number' => '37441119989',
                  'billing_country'    => 'France',
                  'billing_state'      => 'Ain'));
        $userDataParam = $checkoutData['billing_address_data']['billing_first_name'] . ' ' .
                         $checkoutData['billing_address_data']['billing_last_name'];
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        //Steps. Verification Customer group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $checkoutData['billing_address_data']['billing_email']));
        $this->openTab('account_information');
        //Verification
        $this->verifyForm(array('group'=> $data['customerGroups']['group_valid_vat_intraunion']),'account_information');
    }
}