<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'application' => array(
        'url_host' => '127.0.0.1',
        'url_path' => '/',
        'admin' => array(
            'frontname' => 'backend',
            'username' => 'admin',
            'password' => 'password1',
        ),
        'installation' => array(
            'options' => array(
                'option1' => 'value 1',
                'option2' => 'value 2',
            ),
        ),
    ),
    'scenario' => array(
        'common_config' => array(
            'arguments' => array(
                'arg1' => 'value 1',
                'arg2' => 'value 2',
            ),
            'settings' => array(
                'setting1' => 'setting 1',
                'setting2' => 'setting 2',
            ),
        ),
        'scenarios' => array(
            'scenario.jmx' => array(
                'arguments' => array(
                    'arg2' => 'overridden value 2',
                    'arg3' => 'custom value 3',
                ),
                'settings' => array(
                    'setting2' => 'overridden setting 2',
                    'setting3' => 'setting 3',
                ),
                'fixtures' => array(
                    'fixture.php',
                ),
            ),
            'scenario_error.jmx',
            'scenario_failure.jmx' => array(
                'arguments' => array(
                    Magento_Performance_Scenario_Arguments::ARG_HOST => 'crosscutting parameters cannot be redefined',
                ),
                'settings' => array(
                    Magento_Performance_Testsuite::SETTING_SKIP_WARM_UP => true,
                ),
            ),
        ),
    ),
    'report_dir' => 'report',
);
