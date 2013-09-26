<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'attribute_types' => array(
        'text' => array(
            'label' => 'Text'
        )
    ),
    'attribute_groups' => array(
        'event_information' => array(
            'sortOrder'=> '5',
            'visible'   => 'true',
            'label'     => 'Event Information'
        )
    ),
    'registry' => array(
        'static_attributes' => array(
            'event_country' => array(
                'type' => 'country',
                'group' => 'event_information',
                'visible' => 'true',
                'label' => 'Event Country'
            )
        ),
        'custom_attributes' => array(
            'my_event_special' => array(
                'type' => 'country',
                'group' => 'event_information',
                'visible' => 'true',
                'label' => 'My event special'
            )
        )
    ),
    'registrant' => array(
        'static_attributes' => array(
            'role' => array(
                'type' => 'select',
                'group' => 'registrant',
                'visible' => 'true',
                'label' => 'Role'
            )
        ),
        'custom_attributes' => array(
            'my_special_attribute' => array(
                'type' => 'country',
                'group' => 'registrant',
                'visible' => 'true',
                'label' => 'My special attribute'
            )
        )
    )
);