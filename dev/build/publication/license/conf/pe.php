<?php
/**
 * Configuration file used by licence-tool.php script to prepare Magento Professional Edition
 *
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('EDITION_LICENSE', 'MCL');
$config = include __DIR__ . '/ce.php';
return $config;
