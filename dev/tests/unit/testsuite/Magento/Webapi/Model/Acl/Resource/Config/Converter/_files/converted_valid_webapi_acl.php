<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'config' => array(
        'acl' => array(
            'resources' => array(
                array(
                    'id' => 'Custom_Module::resource_one',
                    'title' => 'Resource One Title',
                    'sortOrder' => 20,
                    'disabled' => false,
                    'children' => array(),
                ),
            ),
        ),
        'mapping' => array(
            array(
                'id' => 'Custom_Module::resource_child',
                'parent' => 'Custom_Module::parent_resource',
            ),
            array(
                'id' => 'Custom_Module::resource_two',
                'parent' => 'Custom_Module::resource_one',
            ),
        ),
    ),
);
