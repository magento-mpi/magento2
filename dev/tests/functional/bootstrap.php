<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

session_start();
defined('MTF_BOOT_FILE') || define('MTF_BOOT_FILE', __FILE__);
require_once __DIR__ . '/../../../app/bootstrap.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
restore_error_handler();
require_once __DIR__ . '/vendor/autoload.php';
