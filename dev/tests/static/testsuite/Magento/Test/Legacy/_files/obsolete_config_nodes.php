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
    '/config/install' => 'Configurations moved to DI file settings',
    '/config/install/design' => 'Configurations moved to DI file settings',
    '/config/adminhtml/design' => 'Configurations moved to DI file settings',
    '/config/frontend/design' => 'Configurations moved to DI file settings',
    '/config/crontab' => 'All cron configurations moved to crontab.xml',
);
