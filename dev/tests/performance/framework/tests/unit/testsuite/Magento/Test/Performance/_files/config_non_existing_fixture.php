<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$result = require __DIR__ . '/config_data.php';
$result['scenario']['scenarios']['Scenario']['fixtures'] = array('non_existing_fixture.php');
return $result;
