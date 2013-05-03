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
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->systemConfigurationHelper()->configure($config);
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
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration', false);
        //verify that only one tab is presented on page
        $this->assertEquals(1, $this->getControlCount('tab', 'all_tabs'),
            'Not only "' . $tabName . '" is presented on page.');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement[$tabName] as $fieldset => $fieldsetName) {
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
            array('config_shipping', 'sales_shipping_settings'),
            array('config_contacts', 'general_contacts'),
            array('config_payment_service', 'sales_payment_services'),
            array('config_payment', 'sales_payment_methods'),
            array('config_catalog', 'catalog_catalog'),
            array('config_inventory', 'catalog_inventory'),
            array('config_wishlist', 'customers_wishlist'),
            array('config_google_api', 'sales_google_api'),
            array('config_carries', 'sales_shipping_methods'),
            array('config_cms', 'general_content_management'),
            array('config_newsletter', 'customers_newsletter'),
            array('config_moneybookers', 'sales_moneybookers'),
            array('config_sitemap', 'catalog_xml_sitemap'),
            array('config_persistent', 'customers_persistent_shopping_cart'),
            array('config_reports', 'general_reports'),
            array('config_general', 'general_general'),
            array('config_web', 'general_web'),
            array('config_design', 'general_design'),
            array('config_customers', 'customers_customer_configuration'),
            array('config_tax', 'sales_tax'),
            array('config_sales', 'sales_sales'),
            array('config_sales_email', 'sales_sales_emails'),
            array('config_sales_pdf', 'sales_pdf_print_outs'),
            array('config_checkout', 'sales_checkout'),
            array('config_facebook', 'social_facebook'),
            array('config_system', 'advanced_system'),
            array('config_advanced', 'advanced_advanced'),
            array('config_trans_email', 'general_store_email_addresses'),
            array('config_admin', 'advanced_admin'),
            array('config_developer', 'advanced_developer'),
            array('config_currency', 'general_currency_setup'),
            array('config_rss', 'catalog_rss_feeds'),
            array('config_email_to_friend', 'catalog_email_to_a_friend'),
            array('config_promotion','customers_promotions'),
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
            array('resource_acl' => 'configurations'));
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
        $this->admin('log_in_to_admin', false);
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
        $this->admin('log_in_to_admin', false);
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
