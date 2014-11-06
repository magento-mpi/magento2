<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Shortcut constant for the root directory
 */
define('BP', dirname(__DIR__));

$vendorDir = require BP . '/app/etc/vendor_path.php';
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
