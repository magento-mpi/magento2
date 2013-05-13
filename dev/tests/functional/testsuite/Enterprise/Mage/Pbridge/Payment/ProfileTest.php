<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Mage_Pbridge_Payment_ProfileTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->systemConfigurationHelper()->useHttps('frontend', 'yes');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenet_pb_enable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/payment_bridge_enable');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->systemConfigurationHelper()->useHttps('frontend', 'no');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenet_pb_disable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/payment_bridge_disable');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6461
     */
    public function isProfilePageSecure()
    {
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $login = array('email' => $userData['email'], 'password' => $userData['password']);
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->frontLoginCustomer($login);
        $this->frontend('my_credit_cards');
        $this->assertTrue($this->controlIsVisible('pageelement', 'account_title'));
        $this->assertStringStartsWith('https://', $this->url(), 'Url must be secure');
    }
}