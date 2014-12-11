<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
use Magento\Framework\Autoload\AutoloaderRegistry;
use Magento\Framework\Autoload\ClassLoaderWrapper;

/**
 * Shortcut constant for the root directory
 */
define('BP', dirname(__DIR__));

$vendorDir = require BP . '/app/etc/vendor_path.php';
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";
if (file_exists($vendorAutoload)) {
    $composerAutoloader = include $vendorAutoload;
}
/**
require_once BP . '/lib/internal/Magento/Framework/Autoload/IncludePath.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
$includePath->addIncludePath([BP . '/app/code', BP . '/lib/internal', BP . '/var/generation']);
spl_autoload_register([$includePath, 'load'], true, true);
*/
AutoloaderRegistry::registerAutoloader(new ClassLoaderWrapper($composerAutoloader));

// Sets default autoload mappings, may be overridden in Bootstrap::create
\Magento\Framework\App\Bootstrap::populateAutoloader(BP, []);
