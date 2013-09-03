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
        'version' => '1.0.0.0',
        'active' => true,
        'dependencies' => array(
            'modules' => array(),
            'extensions' => array(
                'strict' => array(
                    array('name' => 'spl'),
                ),
                'alternatives' => array(
                    array(
                        array('name' => 'gd'),
                        array('name' => 'imagick', 'minVersion' => '3.0.0'),
                    ),
                ),
            ),
        ),
    ),
    'Module_Two' => array(
        'name' => 'Module_Two',
        'version' => '2.0.0.0',
        'active' => false,
        'dependencies' => array(
            'modules' => array('Module_One'),
            'extensions' => array(
                'strict' => array(
                    array('name' => 'dom'),
                ),
                'alternatives' => array(),
            ),
        ),
    ),
);
