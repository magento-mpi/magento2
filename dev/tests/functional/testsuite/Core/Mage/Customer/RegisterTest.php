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
 * <p>Customer registration tests</p>
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Customer_RegisterTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->frontend();
        $this->logoutCustomer();
        $this->frontend('customer_login');
    }

    /**
     * <p>Customer registration.  Filling in only required fields</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-3245
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');

        return $userData;
    }

    /**
     * <p>Customer registration.  Use email that already exist.</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3239
     */
    public function withEmailThatAlreadyExists(array $userData)
    {
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'email_exists');
    }

    /**
     * <p>Customer registration. Fill in only required fields. Use max long values for fields.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3242
     */
    public function withLongValues()
    {
        //Data
        $password = $this->generate('string', 255, ':alnum:');
        $userData = $this->loadDataSet('Customers', 'customer_account_register',
            array('first_name'            => $this->generate('string', 255, ':alnum:'),
                  'last_name'             => $this->generate('string', 255, ':alnum:'),
                  'email'                 => $this->generate('email', 128, 'valid'),
                  'password'              => $password,
                  'password_confirmation' => $password,));
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->navigate('edit_account_info');
        //Verifying
        $this->assertTrue($this->verifyForm($userData, null, array('password', 'password_confirmation')),
            $this->getParsedMessages());
    }

    /**
     * <p>Customer registration with empty required field.</p>
     *
     * @param string $field
     * @param string $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3244
     */
    public function withRequiredFieldsEmpty($field, $messageCount)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register', array($field => '%noValue%'));
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->addFieldIdToMessage('field', $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('first_name', 1),
            array('last_name', 1),
            array('email', 1),
            array('password', 2),
            array('password_confirmation', 1)
        );
    }

    /**
     * <p> Customer registration. Fill in all required fields by using special characters(except the field "email").</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3246
     */
    public function withSpecialCharacters()
    {
        //Data
        $password = $this->generate('string', 25, ':punct:');
        $userData = $this->loadDataSet('Customers', 'customer_account_register',
            array('first_name'            => $this->generate('string', 25, ':punct:'),
                  'last_name'             => $this->generate('string', 25, ':punct:'),
                  'email'                 => $this->generate('email', 20, 'valid'),
                  'password'              => $password,
                  'password_confirmation' => $password,));
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
    }

    /**
     * <p> Customer registration. Fill in only required fields. Use value that is greater than the allowable.</p>
     *
     * @param string $fieldName
     *
     * @test
     * @dataProvider withLongValuesNotValidDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3243
     */
    public function withLongValuesNotValid($fieldName)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register',
            array($fieldName => $this->generate('string', 256, ':alnum:')));
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', "not_valid_length_$fieldName");
    }

    public function withLongValuesNotValidDataProvider()
    {
        return array(
            array('first_name'),
            array('last_name'),
        );
    }

    /**
     * <p> Customer registration with invalid value for 'Email' field</p>
     *
     * @param array $invalidEmail
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3240
     */
    public function withInvalidEmail($invalidEmail)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register', $invalidEmail);
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_mail');
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array(array('email' => 'invalid')),
            array(array('email' => 'test@invalidDomain')),
            array(array('email' => 'te@st@unknown-domain.com'))
        );
    }

    /**
     * <p> Customer registration with invalid value for 'Password' fields</p>
     *
     * @param array $invalidPassword
     * @param string $errorMessage
     *
     * @test
     * @dataProvider withInvalidPasswordDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3241
     */
    public function withInvalidPassword($invalidPassword, $errorMessage)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register', $invalidPassword);
        //Steps
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', $errorMessage);
    }

    public function withInvalidPasswordDataProvider()
    {
        return array(
            array(array('password' => 12345, 'password_confirmation' => 12345), 'short_passwords'),
            array(array('password' => 1234567, 'password_confirmation' => 12345678), 'passwords_not_match'),
        );
    }
}