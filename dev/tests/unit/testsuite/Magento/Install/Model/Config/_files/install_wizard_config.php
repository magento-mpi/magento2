<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'steps' => array(
        'begin' => array(
            'name' => 'begin',
            'controller' => 'wizard',
            'action' => 'begin',
            'code' => 'License Agreement'
        ),
        'locale' => array(
            'name' => 'locale',
            'controller' => 'wizard',
            'action' => 'locale',
            'code' => 'Localization',
        ),
    ),
    'filesystem_prerequisites' => array(
        'writables' => array(
            'etc' => array(
                'existence' => '1',
                'recursive' => '0'
            ),
            'var' => array(
                'existence' => '1',
                'recursive' => '1'
            )
        ),
        'notWritables' => array()
    )
);

