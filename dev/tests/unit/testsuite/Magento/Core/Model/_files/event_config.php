<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'event_1' => array(
        'observer_1' => array(
            'instance' => 'instance_1',
            'method' => 'method_name_1',
            'name' => 'observer_1',
        ),
        'observer_5' => array(
            'instance' => 'instance_5',
            'method' => 'method_name_5',
            'name' => 'observer_5',
        )
    ),
    'event_2' => array(
        'observer_2' => array(
            'instance' => 'instance_2',
            'method' => 'method_name_2',
            'disabled' => true,
            'shared' => false,
            'name' => 'observer_2',
        ),
    ),
);