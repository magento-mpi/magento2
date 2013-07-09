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
 * Test creation new customer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Customer_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Customers</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_customer'),
            'There is no "Add New Customer" button on the page');
        $this->clickButton('add_new_customer');
        $this->assertTrue($this->checkCurrentPage('create_customer'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_customer'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create customer by filling in only required fields</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-3587
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return $userData;
    }

    /**
     * <p>Create customer. Use email that already exist</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3582
     */
    public function withEmailThatAlreadyExists(array $userData)
    {
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'customer_email_exist');
    }

    /**
     * <p>Create customer with one empty required field</p>
     *
     * @param string $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3586
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account', array($emptyField => '%noValue%'));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('first_name'),
            array('last_name'),
            array('email')
        );
    }

    /**
     * <p>Create customer. Fill in all fields by using special characters(except the field "email").</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3588
     */
    public function withSpecialCharactersExceptEmail()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('prefix'         => $this->generate('string', 32, ':punct:'),
                  'first_name'     => $this->generate('string', 32, ':punct:'),
                  'middle_name'    => $this->generate('string', 32, ':punct:'),
                  'last_name'      => $this->generate('string', 32, ':punct:'),
                  'suffix'         => $this->generate('string', 32, ':punct:'),
                  'tax_vat_number' => $this->generate('string', 32, ':punct:')));
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('account_information');
        //Verifying
        $this->assertTrue($this->verifyForm($userData, 'account_information'), $this->getParsedMessages());
    }

    /**
     * <p>Create Customer. Fill in fields. Use max long values for fields.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3585
     */
    public function withLongValues()
    {
        //Data
        $longValues = array('prefix'         => $this->generate('string', 255, ':alnum:'),
                            'first_name'     => $this->generate('string', 255, ':alnum:'),
                            'middle_name'    => $this->generate('string', 255, ':alnum:'),
                            'last_name'      => $this->generate('string', 255, ':alnum:'),
                            'suffix'         => $this->generate('string', 255, ':alnum:'),
                            'email'          => $this->generate('email', 128, 'valid'),
                            'tax_vat_number' => $this->generate('string', 255, ':alnum:'));
        $userData = $this->loadDataSet('Customers', 'generic_customer_account', $longValues);
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('account_information');
        //Verifying
        $this->assertTrue($this->verifyForm($userData, 'account_information'), $this->getParsedMessages());
    }

    /**
     * <p>Create customer with invalid value for 'Email' field</p>
     *
     * @param string $wrongEmail
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3583
     */
    public function withInvalidEmail($wrongEmail)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account', array('email' => $wrongEmail));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_email');
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com')
        );
    }

    /**
     * <p>Create customer. Use a value for 'Password' field the length of which less than 6 characters.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3584
     */
    public function withInvalidPassword()
    {
        $this->markTestSkipped('Password field was removed from create customer form: MAGETWO-9619');
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('password' => $this->generate('string', 5, ':alnum:')));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'password_too_short');
    }

    /**
     * <p>Create customer with auto-generated password</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3581
     */
    public function withAutoGeneratedPassword()
    {
        $this->markTestIncomplete('BUG: error message The minimum password length is 6');
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('password' => '%noValue%', 'auto_generated_password' => 'Yes'));
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
    }

    /**
     * <p>Create customer with one address by filling all fields</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3580
     */
    public function withAddress()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'all_fields_customer_account');
        $addressData = $this->loadDataSet('Customers', 'all_fields_address');
        //Steps
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
    }
}