<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    0 => array(
        'id' => 'One_Module::resource',
        'title' => 'Resource One',
        'sortOrder' => 10,
        'children' => array(),
    ),
    1 => array(
        'id' => 'One_Module::resource_parent',
        'title' => 'Resource Parent',
        'sortOrder' => 25,
        'children' => array(
            0 => array(
                'id' => 'One_Module::resource_child_one',
                'title' => 'Resource Child',
                'sortOrder' => 15,
                'children' => array(
                    0 => array(
                        'id' => 'One_Module::resource_child_two',
                        'title' => 'Child Resource Level 2 Title',
                        'sortOrder' => 40,
                        'children' => array(),
                    ),
                ),
            ),
        ),
    ),
);
