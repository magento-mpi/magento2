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
 * <p>Add address tests.</p>
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Customer_AddAddressTest extends Mage_Selenium_TestCase
{
    protected static $_clientTitleParam = '';

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Customers</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('elementTitle', self::$_clientTitleParam);
    }

    /**
     * <p>Create customer for add customer address tests</p>
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        self::$_clientTitleParam = $userData['first_name'] . ' ' . $userData['last_name'];
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return $searchData;
    }

    /**
     * <p>Add address for customer. Fill in only required field.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3604
     */
    public function withRequiredFieldsOnly(array $searchData)
    {
        //Data

        $addressData = $this->loadDataSet('Customers', 'generic_address');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
    }

    /**
     * Add Address for customer with one empty required field.
     *
     * @param string $emptyField
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-3604
     */
    public function withRequiredFieldsEmpty($emptyField, $searchData)
    {
        //Data
        if ($emptyField != 'country') {
            $addressData = $this->loadDataSet('Customers', 'generic_address', array($emptyField => ''));
        } else {
            $addressData = $this->loadDataSet('Customers', 'generic_address', array($emptyField => '',
                                                                                    'state'     => '%noValue%'));
        }
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        // Defining and adding %fieldXpath% for customer Uimap
        $fieldSet = $this->getUimapPage('admin', 'edit_customer')->findFieldset('edit_address');
        if ($emptyField != 'country' and $emptyField != 'state') {
            $fieldXpath = $fieldSet->findField($emptyField);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyField);
        }
        if ($emptyField == 'street_address_line_1') {
            $fieldXpath .= "/ancestor::div[@class='multi-input']";
        }
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('first_name'),
            array('last_name'),
            array('street_address_line_1'),
            array('city'),
            array('country'),
            //array('state'), //Fails because of MAGE-1424 // Should be required only if country='United States'
            array('zip_code'),
            array('telephone')
        );
    }

    /**
     * <p>Add address for customer. Fill in only required field. Use this address as Default Billing.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3601
     */
    public function withDefaultBillingAddress(array $searchData)
    {
        //Data
        $addressData = $this->loadDataSet('Customers', 'all_fields_address', array('default_shipping_address' => 'No'));
        //Steps
        // 1.Open customer
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('addresses');
        //Verifying #–2 - Check saved values
        $addressNumber = $this->customerHelper()->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * <p>Add address for customer. Fill in only required field. Use this address as Default Shipping.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3602
     */
    public function withDefaultShippingAddress(array $searchData)
    {
        $addressData = $this->loadDataSet('Customers', 'all_fields_address', array('default_billing_address' => 'No'));
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('addresses');
        //Verifying #–2 - Check saved values
        $addressNumber = $this->customerHelper()->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * <p>Add address for customer. Fill in all fields by using special characters(except the field "country").</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3605
     */
    public function withSpecialCharactersExceptCountry(array $searchData)
    {
        //Data
        $addressData = $this->loadDataSet('Customers', 'special_char_address');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying #–1
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('addresses');
        //Verifying #–2 - Check saved values
        $addressNumber = $this->customerHelper()->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }

    /**
     * <p>Add address for customer. Fill in only required field. Use max long values for fields.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3603
     */
    public function withLongValuesExceptCountry(array $searchData)
    {
        //Data
        $addressData = $this->loadDataSet('Customers', 'long_values_address');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying #–1
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('addresses');
        //Verifying #–2 - Check saved values
        $addressNumber = $this->customerHelper()->isAddressPresent($addressData);
        $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
    }
}