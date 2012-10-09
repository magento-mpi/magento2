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
        'arguments' => array(
            Magento_Performance_Config_Scenario::ARG_LOOPS => 'A'
        )
    ),
    'defaultConfig' => array(),
    'fixedArguments' => array(),
);
