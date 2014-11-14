<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
define('BP', realpath(__DIR__) . '/');

$vendorDir = require BP . '../../../../../app/etc/vendor_path.php';
$vendorAutoload = BP . "../../../../../{$vendorDir}/autoload.php";
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
