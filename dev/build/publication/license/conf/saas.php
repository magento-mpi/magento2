<?php
/**
 * Configuration file used by licence-tool.php script to prepare Magento Saas Edition
 *
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

$config = require __DIR__ . '/ee.php';
$config['saas'] = array('php' => 'MEL');
return $config;
