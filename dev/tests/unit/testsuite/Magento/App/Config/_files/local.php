<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'connection' => array(
        'example_connection' => array(
            'name' => 'example_connection',
            'dbName' => 'example_db',
        ),
    ),
    'resource' => array(
        'example_resource' => array(
            'name' => 'example_resource',
            'connection' => 'example_connection',
        ),
    ),
    'cache' => array(
        'example_cache' => array(
            'type' => 'example_cache',
            'backend' => 'file',
        ),
    ),
    'another' => 'Example Value',
);
