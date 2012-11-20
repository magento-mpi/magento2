<?php
/**
 * {license_notice}
 *
 * @category   dev
 * @package    build
 * @copyright  {copyright}
 * @license    {license_link}
 */

// Determine application source code location for a tenant
$appBaseDir = '{{app_base_dir}}';

// Determine application configuration string for a tenant
$appConfigString = '';

// Locate the application entry point
/** @var $appEntryPoint callable */
$appEntryPoint = require "$appBaseDir/entry_point_saas.php";

// Delegate execution to the application entry point
$appEntryPoint($appConfigString);
