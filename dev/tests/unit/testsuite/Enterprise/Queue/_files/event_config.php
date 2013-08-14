<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'event_node1' => array(
        'observer_name1' => array(
            'instance' => 'instance_name1',
            'method' => 'method_1',
            'name' => 'observer_name1',
        ),
        'observer_name2' => array(
            'instance' => 'instance_name2',
            'method' => 'method_2',
            'asynchronous' => true,
            'priority' => 10,
            'name' => 'observer_name2',
        ),
    'observer_name3' => array(
            'instance' => 'instance_name2',
            'priority' => 15,
            'name' => 'observer_name3',
        ),
    ),
);