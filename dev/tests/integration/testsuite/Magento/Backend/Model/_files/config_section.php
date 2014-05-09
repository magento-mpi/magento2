<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    array(
        'section' => 'dev',
        'groups' => array(
            'log' => array(
                'fields' => array(
                    'active' => array('value' => '1'),
                    'file' => array('value' => 'fileName.log'),
                    'exception_file' => array('value' => 'exceptionFileName.log')
                )
            ),
            'debug' => array(
                'fields' => array(
                    'template_hints' => array('value' => '1'),
                    'template_hints_blocks' => array('value' => '0')
                )
            )
        ),
        'expected' => array(
            'dev/log' => array(
                'dev/log/active' => '1',
                'dev/log/file' => 'fileName.log',
                'dev/log/exception_file' => 'exceptionFileName.log'
            ),
            'dev/debug' => array('dev/debug/template_hints' => '1', 'dev/debug/template_hints_blocks' => '0')
        )
    )
);
