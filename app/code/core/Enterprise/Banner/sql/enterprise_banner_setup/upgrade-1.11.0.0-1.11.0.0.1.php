<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Enterprise_Banner_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();



echo "<pre>";
//$installer->getConnection()->getForeignKeysTree();

/**
 * Change columns
 */
$tables = array(
    $installer->getTable('enterprise_banner/banner') => array(
        'columns' => array(
            'banner_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Banner Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'is_enabled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Is Enabled'
            ),
            'types' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Types'
            )
        ),
        'comment' => 'Enterprise Banner'
    ),
    $installer->getTable('enterprise_banner/content') => array(
        'columns' => array(
            'banner_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Banner Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'banner_content' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Banner Content'
            )
        ),
        'comment' => 'Enterprise Banner Content'
    ),
    $installer->getTable('enterprise_banner/customersegment') => array(
        'columns' => array(
            'banner_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Banner Id'
            ),
            'segment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Segment Id'
            )
        ),
        'comment' => 'Enterprise Banner Customersegment'
    ),
    $installer->getTable('enterprise_banner/catalogrule') => array(
        'columns' => array(
            'banner_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Banner Id'
            ),
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Id'
            )
        ),
        'comment' => 'Enterprise Banner Catalogrule'
    ),
    $installer->getTable('enterprise_banner/salesrule') => array(
        'columns' => array(
            'banner_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Banner Id'
            ),
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Id'
            )
        ),
        'comment' => 'Enterprise Banner Salesrule'
    )
);

$tables = array(
    $installer->getTable('admin/user') => array(
        'columns' => array(
            'user_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'User ID'
            ),
            'firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'User First Name'
            ),
            'lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'User Last Name'
            ),
            'email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'comment'   => 'User Email'
            ),
            'username' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'User Login'
            ),
            'password' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'User Password'
            ),
            'created' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'User Created Time'
            ),
            'modified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User Modified Time'
            ),
            'logdate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User Last Login Time'
            ),
            'lognum' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User Login Number'
            ),
            'reload_acl_flag' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Reload ACL'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'User Is Active'
            ),
            'extra' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'User Extra Data'
            )
        ),
        'comment' => 'Admin User Table'
    ),
    $installer->getTable('admin/role') => array(
        'columns' => array(
            'role_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Role ID'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Role ID'
            ),
            'tree_level' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Tree Level'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Sort Order'
            ),
            'role_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Type'
            ),
            'user_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User ID'
            ),
            'role_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Role Name'
            )
        ),
        'comment' => 'Admin Role Table'
    ),
    $installer->getTable('admin/rule') => array(
        'columns' => array(
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule ID'
            ),
            'role_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role ID'
            ),
            'resource_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Resource ID'
            ),
            'privileges' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Privileges'
            ),
            'assert_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Assert ID'
            ),
            'role_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'comment'   => 'Role Type'
            ),
            'permission' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'comment'   => 'Permission'
            )
        ),
        'comment' => 'Admin Rule Table'
    ),
    $installer->getTable('admin/assert') => array(
        'columns' => array(
            'assert_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Assert ID'
            ),
            'assert_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Assert Type'
            ),
            'assert_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Assert Data'
            )
        ),
        'comment' => 'Admin Assert Table'
    )
);

$installer->getConnection()->modifyTables($tables);

//print_r($tables);

exit;
function checkUpdate($module)
{
    $basePath = '/storage/data/home/vladimir.pelipenko/dev/mmdb/app/code/core';

    if ($handle = opendir($basePath . '/Enterprise/')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && $file != ".svn") {
                $ee[strtolower('Enterprise_' . $file)] = $file;
            }
        }
        closedir($handle);
    }

    if ($handle = opendir($basePath . '/Mage/')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && $file != ".svn") {
                $ce[strtolower($file)] = $file;
            }
        }
        closedir($handle);
    }
    $template = 'mysql4-modify-columns.php';
    if (strpos($module, 'enterprise') !== false) {
        $update = $basePath . '/Enterprise/' . $ee[$module] . '/sql/' . $module . '_setup/' . $template;
    }
    else {
        $update = $basePath . '/Mage/'. $ce[$module] . '/sql/' . $module . '_setup/' . $template;
    }

    if (!file_exists($update)) {
        //copy('/storage/data/home/vladimir.pelipenko/dev/mmdb/tools/db/' . $template, $update);
        //chmod($update, 0777);
    }
    //unlink($update);

    return $update;
}

echo "<pre>";
//print_r($installer->getConnection()->describeTable($installer->getTable('enterprise_banner/banner')));

$list = unserialize('a:76:{s:4:"core";a:1:{s:6:"tables";a:22:{i:0;s:16:"core_config_data";i:1;s:12:"core_website";i:2;s:10:"core_store";i:3;s:13:"core_resource";i:4;s:10:"core_cache";i:5;s:14:"core_cache_tag";i:6;s:17:"core_cache_option";i:7;s:16:"core_store_group";i:8;s:17:"core_config_field";i:9;s:19:"core_email_template";i:10;s:13:"core_variable";i:11;s:19:"core_variable_value";i:12;s:14:"core_translate";i:13;s:12:"core_session";i:14;s:18:"core_layout_update";i:15;s:16:"core_layout_link";i:16;s:16:"core_url_rewrite";i:17;s:20:"core_url_rewrite_tag";i:18;s:13:"design_change";i:19;s:9:"core_flag";i:20;s:17:"core_file_storage";i:21;s:22:"core_directory_storage";}}s:3:"eav";a:1:{s:6:"tables";a:16:{i:0;s:10:"eav_entity";i:1;s:10:"eav_entity";i:2;s:15:"eav_entity_type";i:3;s:16:"eav_entity_store";i:4;s:20:"eav_entity_attribute";i:5;s:13:"eav_attribute";i:6;s:17:"eav_attribute_set";i:7;s:19:"eav_attribute_group";i:8;s:20:"eav_attribute_option";i:9;s:26:"eav_attribute_option_value";i:10;s:19:"eav_attribute_label";i:11;s:13:"eav_form_type";i:12;s:20:"eav_form_type_entity";i:13;s:17:"eav_form_fieldset";i:14;s:23:"eav_form_fieldset_label";i:15;s:16:"eav_form_element";}}s:7:"install";a:1:{s:6:"tables";a:0:{}}s:5:"admin";a:1:{s:6:"tables";a:4:{i:0;s:10:"admin_user";i:1;s:10:"admin_role";i:2;s:10:"admin_rule";i:3;s:12:"admin_assert";}}s:4:"rule";a:1:{s:6:"tables";a:0:{}}s:9:"adminhtml";a:1:{s:6:"tables";a:0:{}}s:17:"adminnotification";a:1:{s:6:"tables";a:1:{i:0;s:23:"adminnotification_inbox";}}s:4:"cron";a:1:{s:6:"tables";a:1:{i:0;s:13:"cron_schedule";}}s:9:"directory";a:1:{s:6:"tables";a:5:{i:0;s:17:"directory_country";i:1;s:24:"directory_country_format";i:2;s:24:"directory_country_region";i:3;s:29:"directory_country_region_name";i:4;s:23:"directory_currency_rate";}}s:8:"dataflow";a:1:{s:6:"tables";a:7:{i:0;s:16:"dataflow_session";i:1;s:20:"dataflow_import_data";i:2;s:16:"dataflow_profile";i:3;s:24:"dataflow_profile_history";i:4;s:14:"dataflow_batch";i:5;s:21:"dataflow_batch_export";i:6;s:21:"dataflow_batch_import";}}s:3:"cms";a:1:{s:6:"tables";a:4:{i:0;s:8:"cms_page";i:1;s:14:"cms_page_store";i:2;s:9:"cms_block";i:3;s:15:"cms_block_store";}}s:5:"index";a:1:{s:6:"tables";a:3:{i:0;s:11:"index_event";i:1;s:13:"index_process";i:2;s:19:"index_process_event";}}s:8:"customer";a:1:{s:6:"tables";a:7:{i:0;s:15:"customer_entity";i:1;s:23:"customer_address_entity";i:2;s:15:"customer_entity";i:3;s:14:"customer_group";i:4;s:22:"customer_eav_attribute";i:5;s:30:"customer_eav_attribute_website";i:6;s:23:"customer_form_attribute";}}s:7:"catalog";a:1:{s:6:"tables";a:59:{i:0;s:22:"catalog_product_entity";i:1;s:23:"catalog_category_entity";i:2;s:24:"catalog_category_product";i:3;s:30:"catalog_category_product_index";i:4;s:20:"catalog_compare_item";i:5;s:23:"catalog_product_website";i:6;s:29:"catalog_product_enabled_index";i:7;s:25:"catalog_product_link_type";i:8;s:20:"catalog_product_link";i:9;s:30:"catalog_product_link_attribute";i:10;s:38:"catalog_product_link_attribute_decimal";i:11;s:34:"catalog_product_link_attribute_int";i:12;s:38:"catalog_product_link_attribute_varchar";i:13;s:31:"catalog_product_super_attribute";i:14;s:37:"catalog_product_super_attribute_label";i:15;s:39:"catalog_product_super_attribute_pricing";i:16;s:26:"catalog_product_super_link";i:17;s:33:"catalog_product_entity_tier_price";i:18;s:36:"catalog_product_entity_media_gallery";i:19;s:42:"catalog_product_entity_media_gallery_value";i:20;s:22:"catalog_product_option";i:21;s:28:"catalog_product_option_price";i:22;s:28:"catalog_product_option_title";i:23;s:33:"catalog_product_option_type_value";i:24;s:33:"catalog_product_option_type_price";i:25;s:33:"catalog_product_option_type_title";i:26;s:21:"catalog_category_flat";i:27;s:20:"catalog_product_flat";i:28;s:21:"catalog_eav_attribute";i:29;s:24:"catalog_product_relation";i:30;s:25:"catalog_product_index_eav";i:31;s:33:"catalog_product_index_eav_decimal";i:32;s:27:"catalog_product_index_price";i:33;s:32:"catalog_product_index_tier_price";i:34;s:29:"catalog_product_index_website";i:35;s:43:"catalog_product_index_price_cfg_opt_agr_idx";i:36;s:43:"catalog_product_index_price_cfg_opt_agr_tmp";i:37;s:39:"catalog_product_index_price_cfg_opt_idx";i:38;s:39:"catalog_product_index_price_cfg_opt_tmp";i:39;s:37:"catalog_product_index_price_final_idx";i:40;s:37:"catalog_product_index_price_final_tmp";i:41;s:35:"catalog_product_index_price_opt_idx";i:42;s:35:"catalog_product_index_price_opt_tmp";i:43;s:39:"catalog_product_index_price_opt_agr_idx";i:44;s:39:"catalog_product_index_price_opt_agr_tmp";i:45;s:29:"catalog_product_index_eav_idx";i:46;s:29:"catalog_product_index_eav_tmp";i:47;s:37:"catalog_product_index_eav_decimal_idx";i:48;s:37:"catalog_product_index_eav_decimal_tmp";i:49;s:31:"catalog_product_index_price_idx";i:50;s:31:"catalog_product_index_price_tmp";i:51;s:34:"catalog_category_product_index_idx";i:52;s:34:"catalog_category_product_index_tmp";i:53;s:39:"catalog_category_product_index_enbl_idx";i:54;s:39:"catalog_category_product_index_enbl_tmp";i:55;s:37:"catalog_category_anc_categs_index_idx";i:56;s:37:"catalog_category_anc_categs_index_tmp";i:57;s:39:"catalog_category_anc_products_index_idx";i:58;s:39:"catalog_category_anc_products_index_tmp";}}s:11:"catalogrule";a:1:{s:6:"tables";a:5:{i:0;s:11:"catalogrule";i:1;s:19:"catalogrule_product";i:2;s:25:"catalogrule_product_price";i:3;s:28:"catalogrule_affected_product";i:4;s:25:"catalogrule_group_website";}}s:12:"catalogindex";a:1:{s:6:"tables";a:6:{i:0;s:25:"catalog_product_index_eav";i:1;s:27:"catalog_product_index_price";i:2;s:26:"catalogindex_minimal_price";i:3;s:24:"catalogindex_aggregation";i:4;s:28:"catalogindex_aggregation_tag";i:5;s:31:"catalogindex_aggregation_to_tag";}}s:13:"catalogsearch";a:1:{s:6:"tables";a:3:{i:0;s:19:"catalogsearch_query";i:1;s:20:"catalogsearch_result";i:2;s:22:"catalogsearch_fulltext";}}s:5:"sales";a:1:{s:6:"tables";a:48:{i:0;s:16:"sales_flat_quote";i:1;s:21:"sales_flat_quote_item";i:2;s:24:"sales_flat_quote_address";i:3;s:29:"sales_flat_quote_address_item";i:4;s:28:"sales_flat_quote_item_option";i:5;s:24:"sales_flat_quote_payment";i:6;s:30:"sales_flat_quote_shipping_rate";i:7;s:16:"sales_flat_order";i:8;s:21:"sales_flat_order_grid";i:9;s:21:"sales_flat_order_item";i:10;s:24:"sales_flat_order_address";i:11;s:24:"sales_flat_order_payment";i:12;s:31:"sales_flat_order_status_history";i:13;s:18:"sales_order_status";i:14;s:24:"sales_order_status_state";i:15;s:24:"sales_order_status_label";i:16;s:18:"sales_flat_invoice";i:17;s:23:"sales_flat_invoice_grid";i:18;s:23:"sales_flat_invoice_item";i:19;s:26:"sales_flat_invoice_comment";i:20;s:19:"sales_flat_shipment";i:21;s:24:"sales_flat_shipment_grid";i:22;s:24:"sales_flat_shipment_item";i:23;s:27:"sales_flat_shipment_comment";i:24;s:25:"sales_flat_shipment_track";i:25;s:21:"sales_flat_creditmemo";i:26;s:26:"sales_flat_creditmemo_grid";i:27;s:26:"sales_flat_creditmemo_item";i:28;s:29:"sales_flat_creditmemo_comment";i:29;s:23:"sales_recurring_profile";i:30;s:29:"sales_recurring_profile_order";i:31;s:15:"sales_order_tax";i:32;s:28:"sales_flat_order_item_option";i:33;s:18:"sales_order_entity";i:34;s:30:"sales_order_aggregated_created";i:35;s:30:"sales_order_aggregated_updated";i:36;s:25:"sales_shipping_aggregated";i:37;s:31:"sales_shipping_aggregated_order";i:38;s:25:"sales_invoiced_aggregated";i:39;s:31:"sales_invoiced_aggregated_order";i:40;s:25:"sales_refunded_aggregated";i:41;s:31:"sales_refunded_aggregated_order";i:42;s:25:"sales_payment_transaction";i:43;s:34:"sales_bestsellers_aggregated_daily";i:44;s:36:"sales_bestsellers_aggregated_monthly";i:45;s:35:"sales_bestsellers_aggregated_yearly";i:46;s:23:"sales_billing_agreement";i:47;s:29:"sales_billing_agreement_order";}}s:16:"cataloginventory";a:1:{s:6:"tables";a:5:{i:0;s:22:"cataloginventory_stock";i:1;s:27:"cataloginventory_stock_item";i:2;s:29:"cataloginventory_stock_status";i:3;s:33:"cataloginventory_stock_status_idx";i:4;s:33:"cataloginventory_stock_status_tmp";}}s:8:"shipping";a:1:{s:6:"tables";a:1:{i:0;s:18:"shipping_tablerate";}}s:9:"salesrule";a:1:{s:6:"tables";a:8:{i:0;s:9:"salesrule";i:1;s:18:"salesrule_customer";i:2;s:15:"salesrule_label";i:3;s:16:"salesrule_coupon";i:4;s:22:"salesrule_coupon_usage";i:5;s:17:"coupon_aggregated";i:6;s:23:"coupon_aggregated_order";i:7;s:27:"salesrule_product_attribute";}}s:7:"paygate";a:1:{s:6:"tables";a:1:{i:0;s:26:"paygate_authorizenet_debug";}}s:8:"checkout";a:1:{s:6:"tables";a:2:{i:0;s:18:"checkout_agreement";i:1;s:24:"checkout_agreement_store";}}s:6:"backup";a:1:{s:6:"tables";a:0:{}}s:6:"paypal";a:1:{s:6:"tables";a:4:{i:0;s:16:"paypal_api_debug";i:1;s:24:"paypal_settlement_report";i:2;s:28:"paypal_settlement_report_row";i:3;s:11:"paypal_cert";}}s:4:"poll";a:1:{s:6:"tables";a:4:{i:0;s:4:"poll";i:1;s:11:"poll_answer";i:2;s:9:"poll_vote";i:3;s:10:"poll_store";}}s:14:"googlecheckout";a:1:{s:6:"tables";a:2:{i:0;s:24:"googlecheckout_api_debug";i:1;s:27:"googlecheckout_notification";}}s:3:"log";a:1:{s:6:"tables";a:9:{i:0;s:12:"log_customer";i:1;s:11:"log_visitor";i:2;s:16:"log_visitor_info";i:3;s:7:"log_url";i:4;s:12:"log_url_info";i:5;s:11:"log_summary";i:6;s:16:"log_summary_type";i:7;s:9:"log_quote";i:8;s:18:"log_visitor_online";}}s:6:"review";a:1:{s:6:"tables";a:6:{i:0;s:6:"review";i:1;s:13:"review_detail";i:2;s:13:"review_status";i:3;s:13:"review_entity";i:4;s:21:"review_entity_summary";i:5;s:12:"review_store";}}s:6:"rating";a:1:{s:6:"tables";a:7:{i:0;s:6:"rating";i:1;s:12:"rating_store";i:2;s:12:"rating_title";i:3;s:13:"rating_entity";i:4;s:13:"rating_option";i:5;s:18:"rating_option_vote";i:6;s:29:"rating_option_vote_aggregated";}}s:6:"widget";a:1:{s:6:"tables";a:4:{i:0;s:6:"widget";i:1;s:15:"widget_instance";i:2;s:20:"widget_instance_page";i:3;s:27:"widget_instance_page_layout";}}s:3:"tag";a:1:{s:6:"tables";a:4:{i:0;s:3:"tag";i:1;s:12:"tag_relation";i:2;s:11:"tag_summary";i:3;s:14:"tag_properties";}}s:7:"reports";a:1:{s:6:"tables";a:4:{i:0;s:12:"report_event";i:1;s:18:"report_event_types";i:2;s:29:"report_compared_product_index";i:3;s:27:"report_viewed_product_index";}}s:3:"tax";a:1:{s:6:"tables";a:8:{i:0;s:9:"tax_class";i:1;s:15:"tax_calculation";i:2;s:20:"tax_calculation_rate";i:3;s:26:"tax_calculation_rate_title";i:4;s:20:"tax_calculation_rule";i:5;s:28:"tax_order_aggregated_created";i:6;s:28:"tax_order_aggregated_updated";i:7;s:15:"sales_order_tax";}}s:8:"wishlist";a:1:{s:6:"tables";a:3:{i:0;s:8:"wishlist";i:1;s:13:"wishlist_item";i:2;s:20:"wishlist_item_option";}}s:8:"paypaluk";a:1:{s:6:"tables";a:1:{i:0;s:18:"paypaluk_api_debug";}}s:5:"media";a:1:{s:6:"tables";a:0:{}}s:11:"giftmessage";a:1:{s:6:"tables";a:1:{i:0;s:12:"gift_message";}}s:10:"sendfriend";a:1:{s:6:"tables";a:1:{i:0;s:14:"sendfriend_log";}}s:7:"sitemap";a:1:{s:6:"tables";a:1:{i:0;s:7:"sitemap";}}s:3:"rss";a:1:{s:6:"tables";a:0:{}}s:12:"productalert";a:1:{s:6:"tables";a:2:{i:0;s:19:"product_alert_price";i:1;s:19:"product_alert_stock";}}s:15:"googleoptimizer";a:1:{s:6:"tables";a:1:{i:0;s:20:"googleoptimizer_code";}}s:10:"googlebase";a:1:{s:6:"tables";a:3:{i:0;s:16:"googlebase_types";i:1;s:16:"googlebase_items";i:2;s:21:"googlebase_attributes";}}s:14:"amazonpayments";a:1:{s:6:"tables";a:1:{i:0;s:24:"amazonpayments_api_debug";}}s:3:"api";a:1:{s:6:"tables";a:5:{i:0;s:10:"api_assert";i:1;s:8:"api_role";i:2;s:8:"api_rule";i:3;s:8:"api_user";i:4;s:11:"api_session";}}s:6:"bundle";a:1:{s:6:"tables";a:12:{i:0;s:29:"catalog_product_bundle_option";i:1;s:35:"catalog_product_bundle_option_value";i:2;s:32:"catalog_product_bundle_selection";i:3;s:38:"catalog_product_bundle_selection_price";i:4;s:34:"catalog_product_bundle_price_index";i:5;s:34:"catalog_product_bundle_stock_index";i:6;s:38:"catalog_product_index_price_bundle_idx";i:7;s:38:"catalog_product_index_price_bundle_tmp";i:8;s:42:"catalog_product_index_price_bundle_sel_idx";i:9;s:42:"catalog_product_index_price_bundle_sel_tmp";i:10;s:42:"catalog_product_index_price_bundle_opt_idx";i:11;s:42:"catalog_product_index_price_bundle_opt_tmp";}}s:8:"compiler";a:1:{s:6:"tables";a:1:{i:0;s:22:"compiler_configuration";}}s:12:"downloadable";a:1:{s:6:"tables";a:9:{i:0;s:17:"downloadable_link";i:1;s:23:"downloadable_link_title";i:2;s:23:"downloadable_link_price";i:3;s:19:"downloadable_sample";i:4;s:25:"downloadable_sample_title";i:5;s:27:"downloadable_link_purchased";i:6;s:32:"downloadable_link_purchased_item";i:7;s:39:"catalog_product_index_price_downlod_idx";i:8;s:39:"catalog_product_index_price_downlod_tmp";}}s:12:"importexport";a:1:{s:6:"tables";a:1:{i:0;s:23:"importexport_importdata";}}s:5:"ogone";a:1:{s:6:"tables";a:1:{i:0;s:15:"ogone_api_debug";}}s:4:"weee";a:1:{s:6:"tables";a:2:{i:0;s:8:"weee_tax";i:1;s:13:"weee_discount";}}s:10:"newsletter";a:1:{s:6:"tables";a:6:{i:0;s:21:"newsletter_subscriber";i:1;s:16:"newsletter_queue";i:2;s:21:"newsletter_queue_link";i:3;s:27:"newsletter_queue_store_link";i:4;s:19:"newsletter_template";i:5;s:18:"newsletter_problem";}}s:10:"xmlconnect";a:1:{s:6:"tables";a:5:{i:0;s:22:"xmlconnect_application";i:1;s:18:"xmlconnect_history";i:2;s:22:"xmlconnect_application";i:3;s:16:"xmlconnect_queue";i:4;s:32:"xmlconnect_notification_template";}}s:19:"enterprise_admingws";a:1:{s:6:"tables";a:0:{}}s:19:"enterprise_customer";a:1:{s:6:"tables";a:4:{i:0;s:36:"enterprise_customer_sales_flat_order";i:1;s:44:"enterprise_customer_sales_flat_order_address";i:2;s:36:"enterprise_customer_sales_flat_quote";i:3;s:44:"enterprise_customer_sales_flat_quote_address";}}s:23:"enterprise_catalogevent";a:1:{s:6:"tables";a:2:{i:0;s:29:"enterprise_catalogevent_event";i:1;s:35:"enterprise_catalogevent_event_image";}}s:29:"enterprise_websiterestriction";a:1:{s:6:"tables";a:0:{}}s:14:"enterprise_cms";a:1:{s:6:"tables";a:6:{i:0;s:27:"enterprise_cms_page_version";i:1;s:28:"enterprise_cms_page_revision";i:2;s:24:"enterprise_cms_increment";i:3;s:33:"enterprise_cms_hierarchy_metadata";i:4;s:29:"enterprise_cms_hierarchy_node";i:5;s:29:"enterprise_cms_hierarchy_lock";}}s:26:"enterprise_customersegment";a:1:{s:6:"tables";a:4:{i:0;s:34:"enterprise_customersegment_segment";i:1;s:32:"enterprise_customersegment_event";i:2;s:35:"enterprise_customersegment_customer";i:3;s:34:"enterprise_customersegment_website";}}s:26:"enterprise_customerbalance";a:1:{s:6:"tables";a:2:{i:0;s:26:"enterprise_customerbalance";i:1;s:34:"enterprise_customerbalance_history";}}s:17:"enterprise_banner";a:1:{s:6:"tables";a:5:{i:0;s:17:"enterprise_banner";i:1;s:25:"enterprise_banner_content";i:2;s:33:"enterprise_banner_customersegment";i:3;s:29:"enterprise_banner_catalogrule";i:4;s:27:"enterprise_banner_salesrule";}}s:26:"enterprise_giftcardaccount";a:1:{s:6:"tables";a:3:{i:0;s:26:"enterprise_giftcardaccount";i:1;s:31:"enterprise_giftcardaccount_pool";i:2;s:34:"enterprise_giftcardaccount_history";}}s:19:"enterprise_giftcard";a:1:{s:6:"tables";a:1:{i:0;s:26:"enterprise_giftcard_amount";}}s:23:"enterprise_giftregistry";a:1:{s:6:"tables";a:8:{i:0;s:28:"enterprise_giftregistry_type";i:1;s:33:"enterprise_giftregistry_type_info";i:2;s:29:"enterprise_giftregistry_label";i:3;s:30:"enterprise_giftregistry_entity";i:4;s:28:"enterprise_giftregistry_item";i:5;s:35:"enterprise_giftregistry_item_option";i:6;s:30:"enterprise_giftregistry_person";i:7;s:28:"enterprise_giftregistry_data";}}s:23:"enterprise_giftwrapping";a:1:{s:6:"tables";a:3:{i:0;s:23:"enterprise_giftwrapping";i:1;s:40:"enterprise_giftwrapping_store_attributes";i:2;s:31:"enterprise_giftwrapping_website";}}s:29:"enterprise_catalogpermissions";a:1:{s:6:"tables";a:3:{i:0;s:29:"enterprise_catalogpermissions";i:1;s:35:"enterprise_catalogpermissions_index";i:2;s:43:"enterprise_catalogpermissions_index_product";}}s:18:"enterprise_logging";a:1:{s:6:"tables";a:2:{i:0;s:24:"enterprise_logging_event";i:1;s:32:"enterprise_logging_event_changes";}}s:20:"enterprise_pagecache";a:1:{s:6:"tables";a:0:{}}s:14:"enterprise_pci";a:1:{s:6:"tables";a:1:{i:0;s:26:"enterprise_admin_passwords";}}s:19:"enterprise_reminder";a:1:{s:6:"tables";a:5:{i:0;s:24:"enterprise_reminder_rule";i:1;s:32:"enterprise_reminder_rule_website";i:2;s:28:"enterprise_reminder_template";i:3;s:31:"enterprise_reminder_rule_coupon";i:4;s:28:"enterprise_reminder_rule_log";}}s:17:"enterprise_reward";a:1:{s:6:"tables";a:4:{i:0;s:17:"enterprise_reward";i:1;s:25:"enterprise_reward_history";i:2;s:22:"enterprise_reward_rate";i:3;s:27:"enterprise_reward_salesrule";}}s:23:"enterprise_salesarchive";a:1:{s:6:"tables";a:4:{i:0;s:35:"enterprise_sales_order_grid_archive";i:1;s:37:"enterprise_sales_invoice_grid_archive";i:2;s:40:"enterprise_sales_creditmemo_grid_archive";i:3;s:38:"enterprise_sales_shipment_grid_archive";}}s:17:"enterprise_search";a:1:{s:6:"tables";a:1:{i:0;s:29:"catalogsearch_recommendations";}}s:21:"enterprise_invitation";a:1:{s:6:"tables";a:3:{i:0;s:21:"enterprise_invitation";i:1;s:36:"enterprise_invitation_status_history";i:2;s:27:"enterprise_invitation_track";}}s:21:"enterprise_targetrule";a:1:{s:6:"tables";a:7:{i:0;s:21:"enterprise_targetrule";i:1;s:37:"enterprise_targetrule_customersegment";i:2;s:29:"enterprise_targetrule_product";i:3;s:27:"enterprise_targetrule_index";i:4;s:35:"enterprise_targetrule_index_related";i:5;s:37:"enterprise_targetrule_index_crosssell";i:6;s:34:"enterprise_targetrule_index_upsell";}}}');
$list['enterprise_staging']['tables'] = array('enterprise_staging','enterprise_staging_action','enterprise_staging_item','enterprise_staging_log');
$list = array();
$list['catalog']['tables'] = array('eav_entity_datetime','eav_entity_decimal','eav_entity_int','eav_entity_text','eav_entity_varchar');




//$list = array();
//$list['enterprise_banner']['tables'] = array('enterprise_banner',);


print_r($list);


$t_tables = '
$tables = array(
    {tables}
);';

$t_table = '
    \'{table}\' => array(
        \'columns\' => array(
            {columns}
        ),
        \'comment\' => {comment}
    )';

$t_column ='
            \'{column}\' => array(
                \'type\'      => {type},
{adds},
                \'comment\'   => {comment}
            )';

$t_run = '

foreach ($tables as $table => $tableData) {
    foreach ($tableData[\'columns\'] as $column =>$columnDefinition) {
        $installer->getConnection()->modifyColumn($installer->getTable($table), $column, $columnDefinition);
    }
    $installer->getConnection()->changeTableComment($installer->getTable($table), $tableData[\'comment\']);
}
';
$types = array(
        'bool' => 'Varien_Db_Ddl_Table::TYPE_BOOLEAN',
        'smallint' => 'Varien_Db_Ddl_Table::TYPE_SMALLINT',
        'tinyint' => 'Varien_Db_Ddl_Table::TYPE_SMALLINT',
        'int' => 'Varien_Db_Ddl_Table::TYPE_INTEGER',
        'bigint' => 'Varien_Db_Ddl_Table::TYPE_BIGINT',
        'float' => 'Varien_Db_Ddl_Table::TYPE_FLOAT',
        'decimal' => 'Varien_Db_Ddl_Table::TYPE_DECIMAL',
        'date' => 'Varien_Db_Ddl_Table::TYPE_DATE',
        'timestamp' => 'Varien_Db_Ddl_Table::TYPE_TIMESTAMP',
        'text' => 'Varien_Db_Ddl_Table::TYPE_TEXT',
        'longtext' => 'Varien_Db_Ddl_Table::TYPE_LONGTEXT',
        'mediumtext' => 'Varien_Db_Ddl_Table::TYPE_MEDIUMTEXT',
        'varchar' => 'Varien_Db_Ddl_Table::TYPE_TEXT',
        'char' => 'Varien_Db_Ddl_Table::TYPE_TEXT',
        'blob' => 'Varien_Db_Ddl_Table::TYPE_BLOB',
        'mediumblob' => 'Varien_Db_Ddl_Table::TYPE_BLOB'
);

function getColumnComment($showTable, $field)
{
    preg_match("/`".$field."`.* COMMENT ('.*')/", $showTable, $matches);
    try {
    $m = $matches[1];
    } catch (Exception $e) {
        echo $field;
        echo $showTable;
        exit;
    }
    return $m;
}

function getTableComment($showTable)
{
    preg_match("/COMMENT=(.*)/", $showTable, $matches);
    return $matches[1];
}

foreach ($list as $module => $data) {

    if ($module != 'catalog') {
        continue;
    }
    echo $module."<br>";

    $update = checkUpdate($module);
    foreach ($data['tables'] as $table) {

        if (!$installer->getConnection()->isTableExists($table)) {
            continue;
        }
        $describe = $installer->getConnection()->describeTable($table);
        $result = $installer->getConnection()->raw_query('SHOW CREATE TABLE ' . $table);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $showTable = $row['Create Table'];

        foreach($describe as $column => $option) {

            $type = $types[$option['DATA_TYPE']];
            if ($option['DATA_TYPE'] == 'text' || $option['DATA_TYPE'] == 'blob') {
                $option['LENGTH'] = "'64K'";
            }

            $adds = array();
            if ($option['LENGTH']) {
                $adds[] = "                'length'    => " . $option['LENGTH'];
            }
            if ($option['SCALE']) {
                $adds[] = "                'scale'     => ". $option['SCALE'];
            }
            if ($option['PRECISION']) {
                $adds[] = "                'precision' => ". $option['PRECISION'];
            }
            if ($option['IDENTITY']) {
                $adds[] = "                'identity'  => true";
            }
            if ($option['UNSIGNED']) {
                $adds[] = "                'unsigned'  => true";
            }
            if (empty($option['NULLABLE'])) {
                $adds[] = "                'nullable'  => false";
            }
            if ($option['PRIMARY']) {
                $adds[] = "                'primary'   => true";
            }
            if ($option['DEFAULT']) {
                $adds[] = "                'default'   => '".$option['DEFAULT']."'";
            }

            $addition = (count($adds)) ? implode(",\n", $adds) : '';
            $comment = getColumnComment($showTable, $column);

            $columns[] = str_replace(
                array('{column}', '{type}' ,'{comment}', '{adds}'),
                array($column, $type, $comment, $addition),
                $t_column
            );
        }
        $cols = (count($columns)) ? implode(",", $columns) : '';
        $tcomment = getTableComment($showTable);
        $tables[] = str_replace(array('{table}', '{columns}', '{comment}'), array($table, $cols, $tcomment), $t_table);
        $columns = array();
    }

    $tablesData =(count($tables)) ? implode(",\n", $tables) : '';
    $data = str_replace(array('{tables}'), array($tablesData), $t_tables);
    $tables = array();

    echo $data;
exit;
  //  $handle = @fopen($update, 'w+');
  //  if ($handle) {
    //    fwrite($handle, $data . $t_run);
   // }
}

exit;
//echo $data;

$installer->endSetup();