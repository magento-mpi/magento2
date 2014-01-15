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
            'dbName' => 'overridden_db',
        ),
        'new_connection' => array(
            'name' => 'new_connection',
            'dbName' => 'new_db',
        ),
    ),
    'resource' => array(
        'example_resource' => array(
            'name' => 'example_resource',
            'connection' => 'new_connection',
        ),
        'new_resource' => array(
            'name' => 'new_resource',
            'connection' => 'example_connection',
        ),
    ),
    'cache' => array(
        'example_cache' => array(
            'type' => 'example_cache',
            'backend' => 'memcached',
        ),
        'new_cache' => array(
            'type' => 'new_cache',
            'backend' => 'apc',
        ),
    ),
    'another' => 'Overridden Value',
    'new' => 'New Value',
);
