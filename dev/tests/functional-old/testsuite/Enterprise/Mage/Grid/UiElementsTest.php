<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Verification grids into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Grid_UiElementsTest extends Core_Mage_Grid_UiElementsTest
{
    public function setUpBeforeTests()
    {
        parent::setUpBeforeTests();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
    }

    public function uiElementsTestDataProvider()
    {
        return array(
            array('url_rewrite_management'),
            array('manage_admin_users'),
            array('manage_roles'),
            array('system_email_template'),
            array('system_design'),
            array('xml_sitemap'),
            array('manage_attribute_sets'),
            array('search_terms'),
            array('newsletter_problem_reports'),
            array('system_backup'),
            array('manage_tax_zones_and_rates'),
            array('system_custom_variables'),
            array('report_review_customer'),
            array('manage_product_rules'),
            array('manage_reward_rates'),
            array('manage_automated_email_reminder_rules'),
            array('manage_customer_segments'),
            array('manage_gift_registry'),
            array('permissions_locked_users'),
            array('manage_stores'),
            array('report_review_product'),
            array('manage_catalog_events'),
            array('newsletter_queue'),
            array('manage_customer_address_attributes'),
            array('admin_action_log_archive'),
            array('report_statistics'),
            array('theme_list'),
            array('google_content_manage_attributes'),
            array('manage_invitations'),
            array('customer_segment_report'),
            array('report_search'),
            array('manage_ratings'),
            array('admin_action_log_report'),
            array('manage_gift_card_account'),
            array('scheduled_import_export'),
            array('customer_wishlist_report'),
            array('customer_segment_report'),
        );
    }
}
