<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$result = require __DIR__ . '/config_data.php';
unset($result['scenario']['scenarios']['Scenario']['file']);
return $result;
