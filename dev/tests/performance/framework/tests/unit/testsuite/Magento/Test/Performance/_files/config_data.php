<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'application' => array(
        'url_host' => '127.0.0.1',
        'url_path' => '/',
        'installation' => array(
            'options' => array(
                'option1' => 'value 1',
                'option2' => 'value 2',
                'backend_frontname' => 'backend',
                'admin_username' => 'admin',
                'admin_password' => 'password1',
            ),
        ),
    ),
    'scenario' => array(
        'common_config' => array(
            'arguments' => array('arg1' => 'value 1', 'arg2' => 'value 2'),
            'settings' => array('setting1' => 'setting 1', 'setting2' => 'setting 2'),
            'fixtures' => array('fixture2.php')
        ),
        'scenarios' => array(
            'Scenario' => array(
                'file' => 'scenario.jmx',
                'arguments' => array(
                    'arg2' => 'overridden value 2',
                    'arg3' => 'custom value 3',
                    \Magento\TestFramework\Performance\Scenario::ARG_HOST => 'no crosscutting params'
                ),
                'settings' => array('setting2' => 'overridden setting 2', 'setting3' => 'setting 3'),
                'fixtures' => array('fixture.php')
            ),
            'Scenario with Error' => array('file' => 'scenario_error.jmx'),
            'Scenario with Failure' => array(
                'file' => 'scenario_failure.jmx',
                'settings' => array(\Magento\TestFramework\Performance\Testsuite::SETTING_SKIP_WARM_UP => true)
            )
        )
    ),
    'report_dir' => 'report'
);
