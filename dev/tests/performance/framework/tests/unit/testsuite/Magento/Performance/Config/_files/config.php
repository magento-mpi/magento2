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
    'title' => 'Test title',
    'config' => array(
        'file' => 'scenarios/test.php',
        'settings' => array(
            'setting1' => 'valueOverriden',
            'setting2' => 'value2',
        ),
        'arguments' => array(
            Magento_Performance_Config_Scenario::ARG_USERS => 10,
            'arg' => 'val',
            'fixedArg' => 'must not be overriden by scenario'
        ),
        'fixtures' => array(
            'fixtures/fixture1.php',
            'fixtures/fixture2.php',
        ),
    ),
    'defaultConfig' => array(
        'settings' => array(
            'setting1' => 'value1',
            'setting3' => 'value3',
        ),
        'arguments' => array(
            Magento_Performance_Config_Scenario::ARG_LOOPS => 1,
            'argDefault' => 'valueDefault',
            'fixedArg' => 'must not be overriden by common config'
        ),
        'fixtures' => array(
            'fixtures/fixture3.php',
        )
    ),
    'fixedArguments' => array(
        'fixedArg' => 'fixedValue'
    ),
);
