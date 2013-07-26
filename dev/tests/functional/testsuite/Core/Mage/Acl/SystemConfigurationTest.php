<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->logoutAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p> Access available for user with permission to configure only one resource from Configuration menu</p>
     *
     * @param $resourceCheckbox
     * @param $tabName
     *
     * @dataProvider systemConfigurationOneTabDataProvider
     * @test
     * @TestlinkId TL-MAGE-6016
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationOneTab($resourceCheckbox, $tabName)
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        //create user with specific role to verifying ACL permission
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => $resourceCheckbox));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testData['user_name'], 'password' => $testData['password']);
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('system_configuration', false);
        //verify that only one tab is presented on page
        $this->assertEquals(1, $this->getControlCount('tab', 'all_tabs'),
            'Not only "' . $tabName . '" is presented on page.');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default/' . $tabName);
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement as $fieldset => $fieldsetName) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage(
                    'The fieldset "' . $fieldset . '" does not present on tab "' . $tabName . '"');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function systemConfigurationOneTabDataProvider()
    {
        return array(
            array('stores-settings-configuration-general_section', 'general_general'),
            array('stores-settings-configuration-web_section', 'general_web'),
            array('stores-settings-configuration-design_section', 'general_design'),
            array('stores-settings-configuration-currency_setup_section', 'general_currency_setup'),
            array('stores-settings-configuration-store_email_addresses_section', 'general_store_email_addresses'),
            array('stores-settings-configuration-contacts_section', 'general_contacts'),
            array('stores-settings-configuration-google_api', 'general_google_api'),
            array('stores-settings-configuration-reports', 'general_reports'),
            array('stores-settings-configuration-content_management', 'general_content_management'),
            array('stores-settings-configuration-catalog_section', 'catalog_catalog'),
            array('stores-settings-configuration-inventory_section', 'catalog_inventory'),
            array('stores-settings-configuration-xml_sitemap_section', 'catalog_xml_sitemap'),
            array('stores-settings-configuration-rss_feeds_section', 'catalog_rss_feeds'),
            array('stores-settings-configuration-email_to_a_friend', 'catalog_email_to_a_friend'),
            array('stores-settings-configuration-newsletter_section', 'customers_newsletter'),
            array('stores-settings-configuration-customers_section', 'customers_customer_configuration'),
            array('stores-settings-configuration-wishlist_section', 'customers_wishlist'),
            array('stores-settings-configuration-promotion', 'customers_promotions'),
            array('stores-settings-configuration-persistent_shopping_cart', 'customers_persistent_shopping_cart'),
            array('stores-settings-configuration-sales_section', 'sales_sales'),
            array('stores-settings-configuration-sales_emails_section', 'sales_sales_emails'),
            array('stores-settings-configuration-pdf_print_outs', 'sales_pdf_print_outs'),
            array('stores-settings-configuration-tax_section', 'sales_tax'),
            array('stores-settings-configuration-checkout_section', 'sales_checkout'),
            array('stores-settings-configuration-shipping_settings_section', 'sales_shipping_settings'),
            array('stores-settings-configuration-shipping_methods_section', 'sales_shipping_methods'),
            array('stores-settings-configuration-payment_methods_section', 'sales_payment_methods'),
            array('stores-settings-configuration-payment_services', 'sales_payment_services'),
            array('stores-settings-configuration-advanced_admin_section', 'advanced_admin'),
            array('stores-settings-configuration-system_section', 'advanced_system'),
            array('stores-settings-configuration-advanced_section', 'advanced_advanced'),
            array('stores-settings-configuration-developer_section', 'advanced_developer'),
        );
    }

    /**
     * <p>Precondition method</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-settings-configuration'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $testData['user_name'], 'password' => $testData['password']);
    }

    /**
     * <p> Actions available for user with Permission System-Configuration </p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6005
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfiguration($testData)
    {
        $this->adminUserHelper()->loginAdmin($testData);
        //set the configuration scope default config
        $this->navigate('system_configuration');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', 'Default Config');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
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

    /**
     * <p> Actions available for user with Permission System-Configuration </p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6005
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationForWebsite($testData)
    {
        if ($this->getBrowser() == 'chrome') {
            $this->markTestIncomplete('MAGETWO-11392');
        }
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration');
        //set the configuration scope Main Website
        $this->selectStoreScope('dropdown', 'current_configuration_scope', 'Main Website');
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
