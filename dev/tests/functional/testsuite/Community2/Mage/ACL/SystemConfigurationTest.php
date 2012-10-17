<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_ACL_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p> Access available for user with permission to configure only one resource from Configuration menu</p>
     * <p>Steps</p>
     * <p>1. Role "Role1" with permission to configure only one resource from Configuration menu is created</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>3. Login to backend using newly created "User1" credentials.</p>
     * <p>4. Navigate to System-Configuration.</p>
     * <p>5. Verify that only one tab is presented.</p>
     * <p>6. Click on presented tab.</p>
     * <p>Expected result</p>
     * <p> This tab equal to resource from ACL tree.All fieldsets from this tab are presented on page.</p>
     *
     * @param $resourceCheckbox
     * @param $tabName
     *
     * @dataProvider systemConfigurationOneTabDataProvider
     * @test
     * @TestlinkId TL-MAGE-6016
     */
    public function systemConfigurationOneTab($resourceCheckbox, $tabName)
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        //create user with specific role to verifying ACL permission
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Configuration/' . $resourceCheckbox));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration');
        $xpath = $this->_getControlXpath('tab', 'all_tabs');
        //verify that only one tab is presented on page
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)),
            'Not only "' . $tabName . '" is presented on page.');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement[$tabName] as $fieldset=> $fieldsetName) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage(
                    'The fieldset "' . $fieldset . '" does not present on tab "' . $tabName . '"');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function systemConfigurationOneTabDataProvider()
    {
        return array(array('Contacts Section', 'general_contacts'),
                     array('Payment Services', 'sales_payment_services'),
                     array('Payment Methods Section', 'sales_payment_methods'),
                     array('Catalog Section', 'catalog_catalog'),
                     array('Inventory Section', 'catalog_inventory'),
                     array('Wishlist Section', 'customers_wishlist'),
                     array('Google API', 'sales_google_api'),
                     array('Shipping Methods Section', 'sales_shipping_methods'),
                     array('Content Management', 'general_content_management'),
                     array('Shipping Settings Section', 'sales_shipping_settings'),
                     array('Newsletter Section', 'customers_newsletter'),
                     array('Moneybookers Settings', 'sales_moneybookers'),
                     array('Google Sitemap Section', 'catalog_google_sitemap'),
                     array('Magento Core API Section', 'services_magento_core_api'),
                     array('Persistent Shopping Cart', 'customers_persistent_shopping_cart'),
                     array('Reports', 'general_reports'),
                     array('OAuth', 'services_oauth'),
                     array('General Section', 'general_general'),
                     array('Web Section', 'general_web'),
                     array('Design Section', 'general_design'),
                     array('Customers Section', 'customers_customer_configuration'),
                     array('Tax Section', 'sales_tax'),
                     array('Sales Section', 'sales_sales'),
                     array('Sales Emails Section', 'sales_sales_emails'),
                     array('PDF Print-outs', 'sales_pdf_print_outs'),
                     array('Checkout Section', 'sales_checkout'),
                     array('Facebook Section', 'social_facebook'),
                     array('System Section', 'advanced_system'),
                     array('Advanced Section', 'advanced_advanced'),
                     array('Store Email Addresses Section', 'general_store_email_addresses'),
                     array('Advanced Admin Section', 'advanced_admin'),
                     array('Developer Section', 'advanced_developer'),
                     array('Currency Setup Section', 'general_currency_setup'),
                     array('RSS Feeds Section', 'catalog_rss_feeds'),
                     array('Email to a Friend', 'catalog_email_to_a_friend'),
        );
    }

    /**
     * <p>Precondition method</p>
     * <p>Admin user with Role System-Configuration is created.</p>
     * <p>Steps:</p>
     * <p>1. Create new admin role with "Role Resources":</p>
     * <p>1.1 Resource Access = Custom</p>
     * <p>1.2 Resource checkboxes = 'System-Configuration'</p>
     * <p>2. Create test Admin user with test Role(Full permissions for Configuration menu)</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Configuration'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return $testData;
    }

    /**
     * <p> Actions available for user with Permission System-Configuration </p>
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with Role resource System-Configuration is created.</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>Steps</p>
     * <p>1. Log in to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Configuration.</p>
     * <p>3. Click to all tab(one after the other) in configuration menu.(On the left column).</p>
     * <p>Expected result</p>
     * <p>All configuration pages(one after other) are successfully opened.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6005
     */
    public function systemConfiguration($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        //set the configuration scope default config
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', 'Default Config');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verifying that all necessary tabs and fieldsets are present
        foreach ($tabElement as $tab=> $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
            foreach ($tabName as $fieldset=> $fieldsetName) {
                if (!$this->controlIsPresent('fieldset', $fieldset)) {
                    $this->addVerificationMessage(
                        'The fieldset "' . $fieldset . '" does not present on tab "' . $tab . '"');
                }
            }
        }

        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p> Actions available for user with Permission System-Configuration </p>
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with Role resource System-Configuration is created.</p>
     * <p>2. Admin user  "User1" with "Role1" is created.</p>
     * <p>Steps</p>
     * <p>1. Log in to backend using newly created "User1" credentials </p>
     * <p>2. Navigate to System-Configuration.</p>
     * <p>3. Set configuration scope:"Main website"</p>
     * <p>4. Click to all tab(one after the other) in configuration menu.(On the left column).</p>
     * <p>Expected result</p>
     * <p>All configuration pages(one after other) are successfully opened.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6005
     */
    public function systemConfigurationForWebsite($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration');
        //set the configuration scope Main Website
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', 'Main Website');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_website');
        //verifying that all necessary tabs and fieldsets are present
        foreach ($tabElement as $tab => $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
            foreach ($tabName as $fieldset => $fieldsetName) {
                if (!$this->controlIsPresent('fieldset', $fieldset)) {
                    $this->addVerificationMessage(
                        'The fieldset "' . $fieldset . '" does not present on tab "' . $tab . '"');
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}

