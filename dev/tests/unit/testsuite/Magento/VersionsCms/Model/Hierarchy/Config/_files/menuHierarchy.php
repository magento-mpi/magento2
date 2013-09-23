<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'content' => array(
        'name' => 'content',
        'label' => 'Content',
        'handle' => 'versionscms_hierarchy_menu_content',
        'isDefault' => true,
        'pageLayoutHandles' => array(
        ),
    ),
    'left_column' => array(
        'name' => 'left_column',
        'label' => 'Left Column',
        'handle' => 'versionscms_hierarchy_menu_left_column',
        'pageLayoutHandles' => array(
            'page_two_columns_left',
            'page_three_columns'
        ),
    )
);
