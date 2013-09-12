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
    '/config/adminhtml/menu'                   => 'Move them to adminhtml.xml.',
    '/config/adminhtml/acl'                    => 'Move them to adminhtml.xml.',
    '/config/*/events/core_block_abstract_to_html_after' =>
    'Event has been replaced with "core_layout_render_element"',
    '/config/*/events/catalog_controller_product_delete' => '',
    '/config//observers/*/args' => 'This was an undocumented and unused feature in event subscribers',
    '/config/default/design/theme' => 'Relocated to /config/<area>/design/theme',
    '/config/default/web/*/base_js_url' => '/config/default/web/*/base_lib_url',
    '/config/default/web/*/base_skin_url' => '/config/default/web/*/base_static_url',
    '/config/global/cache/types/*/tags' => 'use /config/global/cache/types/*/class node instead',
    '/config/global/disable_local_modules' => '',
    '/config/*/layout' => 'Use convention for layout files placement instead of configuration',
    '/config/frontend/product/collection/attributes'
        => 'Use /config/group[@name="catalog_product"] of attributes.xml',
    '/config/frontend/category/collection/attributes'
        => 'Use /config/group[@name="catalog_category"] of attributes.xml',
    '/config/global/sales/quote/item/product_attributes'
        => 'Use /config/group[@name="sales_quote_item"] of attributes.xml',
    '/config/global/wishlist/item/product_attributes'
        => 'Use /config/group[@name="wishlist_item"] of attributes.xml',
    '/config/global/catalog/product/flat/attribute_nodes'
        => 'Use /config/global/catalog/product/flat/attribute_groups',
);
