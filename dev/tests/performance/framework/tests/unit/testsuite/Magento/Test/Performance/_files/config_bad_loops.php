<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$result = require __DIR__ . '/config_data.php';
$result['scenario']['scenarios']['Scenario']['arguments'] = [
    \Magento\TestFramework\Performance\Scenario::ARG_LOOPS => 'A',
];
return $result;
