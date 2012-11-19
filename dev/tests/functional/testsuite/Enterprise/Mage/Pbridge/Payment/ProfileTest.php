<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterpise_Pbridge
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Mage_Pbridge_Payment_ProfileTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Create customer and set necessary system configuration options</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');

        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->useHttps('frontend');

        $this->_toggleAuthorizePBConfiguration();
        $this->_togglePaymentBridgeConfiguration();

        return $userData;
    }

    /**
     * Enable/Disable Authorize.net (Payment Bridge only) configuration
     *
     * @param bool $enable
     */
    protected function _toggleAuthorizePBConfiguration($enable = true)
    {
        $key = 'enable';
        if (!$enable) {
            $key = 'disable';
        }

        $paymentConfiguration = $this->loadDataSet('PaymentMethod', 'authorizenetpb_' . $key);
        $this->systemConfigurationHelper()->configure($paymentConfiguration);
    }

    /**
     * Enable/Disable Payment Bridge configuration
     *
     * @param bool $enable
     */
    protected function _togglePaymentBridgeConfiguration($enable = true)
    {
        $key = 'enable';
        if (!$enable) {
            $key = 'disable';
        }

        $paymentConfiguration = $this->loadDataSet('PaymentMethod', 'payment_bridge_' . $key);
        unset($paymentConfiguration['configuration_scope']);
        $this->systemConfigurationHelper()->configure($paymentConfiguration);
    }

    /**
     * @test
     * @author azavadsky
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6461
     *
     * @param array $userData
     */
    public function isProfilePageSecure(array $userData)
    {
        $this->customerHelper()->frontLoginCustomer(
            array(
                'email'    => $userData['email'],
                'password' => $userData['password']
            )
        );

        $page = 'my_credit_cards';
        $pageUrl = $this->_uimapHelper->getPageUrl('frontend', $page);
        if (substr($pageUrl, 0, 5) === 'https') {
            $pageUrl = str_replace('https://', 'http://', $pageUrl);
        }
        $this->open($pageUrl);
        $this->validatePage($page);
        $this->assertTrue($this->controlIsPresent('pageelement', 'account_title'));

        $this->assertStringStartsWith('https://', $this->getLocation(), 'Url must be secure');
    }

    /**
     * @test
     * @depends preconditionsForTests
     */
    public function disableConfiguration()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->useHttps('frontend', 'No');

        $this->_toggleAuthorizePBConfiguration(false);
        $this->_togglePaymentBridgeConfiguration(false);
    }
}
