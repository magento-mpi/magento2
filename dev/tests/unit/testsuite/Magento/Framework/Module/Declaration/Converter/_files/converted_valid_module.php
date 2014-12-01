<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'Module_One' => array(
        'name' => 'Module_One',
        'schema_version' => '1.0.0.0',
        'sequence' => array(),
    ),
    'Module_Two' => array(
        'name' => 'Module_Two',
        'schema_version' => '2.0.0.0',
        'sequence' => array('Module_One'),
    )
);
