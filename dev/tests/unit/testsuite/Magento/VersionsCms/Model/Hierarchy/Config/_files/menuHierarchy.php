<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'content' => [
        'name' => 'content',
        'label' => 'Content',
        'handle' => 'versionscms_hierarchy_menu_content',
        'isDefault' => true,
        'pageLayoutHandles' => [],
    ],
    'left_column' => [
        'name' => 'left_column',
        'label' => 'Left Column',
        'handle' => 'versionscms_hierarchy_menu_left_column',
        'pageLayoutHandles' => ['page_two_columns_left', 'page_three_columns'],
    ]
];
