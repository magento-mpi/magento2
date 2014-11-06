<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'connection' => array(
        'connection_one' => array('name' => 'connection_one', 'dbname' => 'overridden_db_one'),
        'connection_new' => array('name' => 'connection_new', 'dbname' => 'db_new')
    ),
    'resource' => array(
        'resource_one' => array('name' => 'resource_one', 'connection' => 'connection_new'),
        'resource_new' => array('name' => 'resource_new', 'connection' => 'connection_two')
    ),
    'cache' => array(
        'frontend' => array(
            'cache_frontend_one' => array('name' => 'cache_frontend_one', 'backend' => 'memcached'),
            'cache_frontend_new' => array('name' => 'cache_frontend_new', 'backend' => 'apc')
        ),
        'type' => array(
            'cache_type_one' => array('name' => 'cache_type_one', 'frontend' => 'cache_frontend_new'),
            'cache_type_new' => array('name' => 'cache_type_new', 'frontend' => 'cache_frontend_two')
        )
    ),
    'arbitrary_one' => 'Overridden Value One',
    'arbitrary_new' => 'Value New'
);
