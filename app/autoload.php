<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
$vendorDir = require __DIR__ . '/etc/vendor_path.php';
require_once __DIR__ . '/../' . $vendorDir . '/autoload.php';
require_once __DIR__ . '/../lib/internal/Magento/Framework/Autoload/IncludePath.php';
spl_autoload_register([new \Magento\Framework\Autoload\IncludePath(), 'load'], true, true);
