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

$result = require __DIR__ . '/config_data.php';
$result['scenario']['scenarios'] = array('non_existing_scenario.jmx');
return $result;
