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
        'connection_two' => array('name' => 'connection_two', 'dbname' => 'db_two'),
        'connection_new' => array('name' => 'connection_new', 'dbname' => 'db_new')
    ),
    'resource' => array(
        'resource_one' => array('name' => 'resource_one', 'connection' => 'connection_new'),
        'resource_two' => array('name' => 'resource_two', 'connection' => 'connection_two'),
        'resource_new' => array('name' => 'resource_new', 'connection' => 'connection_two')
    ),
    'cache' => array(
        'frontend' => array(
            'cache_frontend_one' => array('name' => 'cache_frontend_one', 'backend' => 'memcached'),
            'cache_frontend_two' => array('name' => 'cache_frontend_two', 'backend' => 'file'),
            'cache_frontend_new' => array('name' => 'cache_frontend_new', 'backend' => 'apc')
        ),
        'type' => array(
            'cache_type_one' => array('name' => 'cache_type_one', 'frontend' => 'cache_frontend_new'),
            'cache_type_two' => array('name' => 'cache_type_two', 'frontend' => 'cache_frontend_two'),
            'cache_type_new' => array('name' => 'cache_type_new', 'frontend' => 'cache_frontend_two')
        )
    ),
    'arbitrary_one' => 'Overridden Value One',
    'arbitrary_two' => 'Value Two',
    'huge_nested_level' => array(
        'level_one' => array(
            'level_two' => array('level_three' => array('level_four' => array('level_five' => 'Level Five Data')))
        )
    ),
    'arbitrary_new' => 'Value New'
);
