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
    '/config/general/locale'                   =>
        'This configuration moved to Di configuration of \Magento\Locale\ConfigInterface',
    '/config/global/can_use_base_url'          =>
        'This configuration moved to Di configuration of \Magento\Backend\App\Action\Context class',
    '/config/global/locale/allow/codes'        =>
        'This configuration moved to Di configuration of \Magento\Locale\ConfigInterface',
    '/config/global/locale/allow/currencies'   =>
        'This configuration moved to Di configuration of \Magento\Locale\ConfigInterface',
    '/config/global/mime/types'                =>
        'This configuration moved to Di configuration for \Magento\Downloadable\Helper\File class',
    '/config/global/models/*/deprecatedNode'   => '',
    '/config/global/models/*/entities/*/table' => '',
    '/config/global/models/*/class'            => '',
    '/config/global/helpers/*/class'           => '',
    '/config/global/blocks/*/class'            => '',
    '/config/global/models/*/resourceModel'    => '',
    '/config/global/page/layouts'              => 'Moved to page_layouts.xml',
    '/config/global/cms/layouts'               => 'This was never used and is no longer supported',
    '/config/global/payment/cc/types/*/validator' =>
        'This configuration was moved to DI configuration of \Magento\Centinel\Model\StateFactory',
    '/config/global/payment'                   => 'Move them to payment.xml.',
    '/config/adminhtml/menu'                   => 'Move them to adminhtml.xml.',
    '/config/adminhtml/acl'                    => 'Move them to adminhtml.xml.',
    '/config/adminhtml/global_search'          =>
        'This configuration moved to Di configuration of \Magento\Backend\Controller\Index',
    '/config/*[self::global|self::adminhtml|self::frontend]/di' => 'This configuration moved to di.xml file',
    '/config/*[self::global|self::adminhtml|self::frontend]/events' => 'This configuration moved to events.xml file',
    '/config/*[self::global|self::adminhtml|self::frontend]/routers' =>
        'Routes configuration moved to routes.xml file,'
        . 'routers list can be set through Di configuration of \Magento\App\RouterList model',
    '/config/global/importexport' => 'This configuration moved to import.xml and export.xml files',
    '/config/global/catalog/product/type' => 'This configuration moved to product_types.xml file',
    '/config/global/catalog/product/options' => 'This configuration moved to product_options.xml file',
    '/config/global/catalog/product/media/image_types' =>
        'This configuration moved to Di configuration of '
        . '\Magento\Backend\Block\Catalog\Product\Frontend\Product\Watermark',
    '/config/global/eav_attributes' => 'This configuration moved to eav_attributes.xml file',
    '/config/global/index' => 'This configuration moved to indexers.xml file',
    '/config/global/catalogrule' => 'This configuration moved to Di configuration of \Magento\CatalogRule\Model\Rule',
    '/config/global/salesrule' => 'This configuration moved to Di configuration of \Magento\SalesRule\Helper\Coupon',
    '/config/global/session' => 'This configuration moved to Di configuration of \Magento\Core\Model\Session\Validator',
    '/config/global/ignore_user_agents' => 'This configuration moved to Di configuration of \Magento\Log\Model\Visitor',
    '/config/global/request' => 'This configuration moved to Di configuration of \Magento\App\RequestInterface',
    '/config/global/secure_url' =>
        'This configuration moved to Di configuration of \Magento\Core\Model\Url\SecurityInfo',
    '/config/global/dev' =>
        'This configuration moved to Di configuration of \Magento\App\Action\Context',
    '/config/global/webapi' =>
        'This configuration moved to Di configuration of \Magento\Webapi\Controller\Request\Rest\Interpreter\Factory'
        . ' and \Magento\Webapi\Controller\Response\Rest\Renderer\Factory',
    '/config/global/cms' => 'This configuration moved to Di configuration of \Magento\Cms\Model\Wysiwyg\Images\Storage'
        .' and \Magento\Cms\Model\Wysiwyg\Config',
    '/config/global/widget' =>
        'This configuration moved to Di configuration of \Magento\Cms\Model\Template\FilterProvider',
    '/config/global/catalog/product/flat/max_index_count' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Model\Resource\Product\Flat\Indexer',
    '/config/global/catalog/product/flat/attribute_groups' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Model\Resource\Product\Flat\Indexer',
    '/config/global/catalog/product/flat/add_filterable_attributes' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Helper\Product\Flat',
    '/config/global/catalog/product/flat/add_child_data' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Helper\Product\Flat',
    '/config/global/catalog/content/template_filter' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Helper\Data',
    '/config/frontend/catalog/per_page_values/list' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Model\Config\Source\ListPerPage',
    '/config/frontend/catalog/per_page_values/grid' =>
        'This configuration moved to Di configuration of \Magento\Catalog\Model\Config\Source\GridPerPage',
    '/config/global/catalog/product/design' =>
        'This configuration moved to Di configuration of'
        . ' \Magento\Catalog\Model\Entity\Product\Attribute\Design\Option\Container',
    '/config/global/catalog/product/attributes' =>
        'This configuration moved catalog_attributes.xml',
    '/config/global/eav_frontendclasses' =>
        'This configuration was removed. '
        . 'Please pluginize \Magento\Eav\Helper\Data::getFrontendClasses to extend frontend classes list',
    '/config/global/resources' => 'This configuration moved to Di configuration of \Magento\App\Resource',
    '/config/global/resource' => 'This configuration moved to Di configuration of \Magento\App\Resource',
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
    '/config/global/areas' => 'Configurations moved to DI file settings',
    '/config/global/configurators' => 'Solr proxy classes uses instead',
    '/config/vde' => 'Was moved to di',
    '/config/global/ignoredModules' => 'Was replaced using di',
    '/config/global/helpers' => 'Was replaced using di',
    '/config/global/external_cache' => 'Was replaced using di',
    '/config/global/currency/import/services' => 'Configurations moved to DI file settings',
    '/config/global/template' => 'Use /config/template of email_templates.xml',
);
