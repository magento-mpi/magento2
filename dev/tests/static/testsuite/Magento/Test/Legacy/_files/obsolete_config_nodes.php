<?php
/**
 * Obsolete configuration nodes
 *
 * Format: <class_name> => <replacement>
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '/config/global/fieldsets'                 => '',
    '/config/global/cache/betatypes'           => '',
    '/config/admin/fieldsets'                  => '',
    '/config/global/models/*/deprecatedNode'   => '',
    '/config/global/models/*/entities/*/table' => '',
    '/config/global/models/*/class'            => '',
    '/config/global/helpers/*/class'           => '',
    '/config/global/blocks/*/class'            => '',
    '/config/global/models/*/resourceModel'    => '',
    '/config/global/page/layouts'              => 'Moved to page_layouts.xml',
    '/config/global/cms/layouts'               => 'This was never used and is no longer supported',
    '/config/adminhtml/menu'                   => 'Move them to adminhtml.xml.',
    '/config/adminhtml/acl'                    => 'Move them to adminhtml.xml.',
    '/config/*[self::global|self::adminhtml|self::frontend]/di' => 'This configuration moved to di.xml file',
    '/config/*[self::global|self::adminhtml|self::frontend]/events' => 'This configuration moved to events.xml file',
    '/config/*[self::global|self::adminhtml|self::frontend]/routers' =>
        'Routes configuration moved to routes.xml file,'
        . 'routers list can be set through Di configuration of Magento_Core_Model_RouterList model',
    '/config/global/importexport' => 'This configuration moved to import.xml and export.xml files',
    '/config/global/catalog/product/type' => 'This configuration moved to product_types.xml file',
    '/config/global/catalog/product/options' => 'This configuration moved to product_options.xml file',
    '/config/global/eav_attributes' => 'This configuration moved to eav_attributes.xml file',
    '/config/global/index' => 'This configuration moved to indexers.xml file',
    '/config/global/catalogrule' => 'This configuration moved to Di configuration of Magento_CatalogRule_Model_Rule',
    '/config/global/salesrule' => 'This configuration moved to Di configuration of Magento_SalesRule_Helper_Coupon',
    '/config/global/session' => 'This configuration moved to Di configuration of Magento_Core_Model_Session_Validator',
    '/config/global/ignore_user_agents' => 'This configuration moved to Di configuration of Magento_Log_Model_Visitor',
    '/config/global/request' => 'This configuration moved to Di configuration of Magento_Core_Controller_Request_Http',
    '/config/global/secure_url' =>
        'This configuration moved to Di configuration of Magento_Core_Model_Url_SecurityInfo',
    '/config/global/dev' =>
        'This configuration moved to Di configuration of Magento_Core_Controller_Varien_Action_Context',
    '/config/global/webapi' =>
        'This configuration moved to Di configuration of Magento_Webapi_Controller_Request_Rest_Interpreter_Factory'
        . ' and Magento_Webapi_Controller_Response_Rest_Renderer_Factory',
    '/config/global/cms' => 'This configuration moved to Di configuration of Magento_Cms_Model_Wysiwyg_Images_Storage'
        .' and Magento_Cms_Model_Wysiwyg_Config',
    '/config/global/widget' =>
        'This configuration moved to Di configuration of Magento_Cms_Model_Template_FilterProvider',
    '/config/global/catalog/product/flat/max_index_count' =>
        'This configuration moved to Di configuration of Magento_Catalog_Model_Resource_Product_Flat_Indexer',
    '/config/global/catalog/product/flat/attribute_groups' =>
        'This configuration moved to Di configuration of Magento_Catalog_Model_Resource_Product_Flat_Indexer',
    '/config/global/catalog/product/flat/add_filterable_attributes' =>
        'This configuration moved to Di configuration of Magento_Catalog_Helper_Product_Flat',
    '/config/global/catalog/product/flat/add_child_data' =>
        'This configuration moved to Di configuration of Magento_Catalog_Helper_Product_Flat',
    '/config/global/catalog/content/template_filter' =>
        'This configuration moved to Di configuration of Magento_Catalog_Helper_Data',
    '/config/frontend/catalog/per_page_values/list' =>
        'This configuration moved to Di configuration of Magento_Catalog_Model_Config_Source_ListPerPage',
    '/config/frontend/catalog/per_page_values/grid' =>
        'This configuration moved to Di configuration of Magento_Catalog_Model_Config_Source_GridPerPage',
    '/config/global/catalog/product/design' =>
        'This configuration moved to Di configuration of'
        . ' Magento_Catalog_Model_Entity_Product_Attribute_Design_Option_Container',
    '/config/global/catalog/product/attributes' =>
        'This configuration moved catalog_attributes.xml',
    '/config/global/eav_frontendclasses' =>
        'This configuration was removed. '
        . 'Please pluginize Magento_Eav_Helper_Data::getFrontendClasses to extend frontend classes list',
    '/config/*/events/core_block_abstract_to_html_after' =>
    'Event has been replaced with "core_layout_render_element"',
    '/config/*/events/catalog_controller_product_delete' => '',
    '/config//observers/*/args' => 'This was an undocumented and unused feature in event subscribers',
    '/config/default/design/theme' => 'Relocated to /config/<area>/design/theme',
    '/config/global/theme' => 'Configuration moved to DI file settings',
    '/config/default/web/*/base_js_url' => '/config/default/web/*/base_lib_url',
    '/config/default/web/*/base_skin_url' => '/config/default/web/*/base_static_url',
    '/config/global/cache/types/*/tags' => 'use /config/global/cache/types/*/class node instead',
    '/config/global/disable_local_modules' => '',
    '/config/global/newsletter/tempate_filter' => 'Use DI configs to setup model for template processing',
    '/config/*/layout' => 'Use convention for layout files placement instead of configuration',
    '/config/frontend/product/collection/attributes'
        => 'Use /config/group[@name="catalog_product"] of catalog_attributes.xml',
    '/config/frontend/category/collection/attributes'
        => 'Use /config/group[@name="catalog_category"] of catalog_attributes.xml',
    '/config/global/sales/quote/item/product_attributes'
        => 'Use /config/group[@name="sales_quote_item"] of catalog_attributes.xml',
    '/config/global/wishlist/item/product_attributes'
        => 'Use /config/group[@name="wishlist_item"] of catalog_attributes.xml',
    '/config/global/catalog/product/flat/attribute_nodes'
        => 'Use /config/global/catalog/product/flat/attribute_groups',
    '/config/global/customer/address/formats' => 'Use /config/format of address_formats.xml',
    '/config/global/pdf' => 'Use configuration in pdf.xml',
    '/config/install' => 'Configurations moved to DI file settings',
    '/config/install/design' => 'Configurations moved to DI file settings',
    '/config/adminhtml/design' => 'Configurations moved to DI file settings',
    '/config/frontend/design' => 'Configurations moved to DI file settings',
    '/config/crontab' => 'All cron configurations moved to crontab.xml',
    '/config/vde' => 'Configurations moved to DI file settings',
    '/config/global/currency/import/services' => 'Configurations moved to DI file settings',
);
