<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'connection' => array(
        'connection_one' => array('name' => 'connection_one', 'dbname' => 'db_one'),
        'connection_two' => array('name' => 'connection_two', 'dbname' => 'db_two')
    ),
    'resource' => array(
        'resource_one' => array('name' => 'resource_one', 'connection' => 'connection_one'),
        'resource_two' => array('name' => 'resource_two', 'connection' => 'connection_two')
    ),
    'cache' => array(
        'frontend' => array(
            'cache_frontend_one' => array('name' => 'cache_frontend_one', 'backend' => 'blackHole'),
            'cache_frontend_two' => array('name' => 'cache_frontend_two', 'backend' => 'file')
        ),
        'type' => array(
            'cache_type_one' => array('name' => 'cache_type_one', 'frontend' => 'cache_frontend_one'),
            'cache_type_two' => array('name' => 'cache_type_two', 'frontend' => 'cache_frontend_two')
        )
    ),
    'arbitrary_one' => 'Value One',
    'arbitrary_two' => 'Value Two',
    'huge_nested_level' => array(
        'level_one' => array(
            'level_two' => array('level_three' => array('level_four' => array('level_five' => 'Level Five Data')))
        )
    )
);
