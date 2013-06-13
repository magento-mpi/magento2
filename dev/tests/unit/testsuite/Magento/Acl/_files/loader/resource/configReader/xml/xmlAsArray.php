<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'node_one' => array(
        '__attributes__' => array('id' => 'resource_1', 'title' => 'Resource 1', 'sortOrder' => 1, 'disabled' => '0'),
        'resource' => array(
            array(
                '__attributes__' => array(
                    'id' => 'resource_1.3', 'title' => 'Resource 1.3', 'disabled' => '1', 'sortOrder' => 2
                ),
            ),
            array(
                '__attributes__' => array(
                    'id' => 'resource_1.1', 'title' => 'Resource 1.1', 'disabled' => 1
                ),
            ),
            array(
                '__attributes__' => array(
                    'id' => 'resource_1.2', 'title' => 'Resource 1.2', 'sortOrder' => 1, 'disabled' => 'false'
                ),
            ),
        ),
    ),
    'node_two' => array(
        '__attributes__' => array('id' => 'resource_2', 'title' => 'Resource 2', 'disabled' => 'true'),
    ),
);
