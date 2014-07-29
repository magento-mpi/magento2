<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
if (file_exists(__DIR__ . '/vendor_autoload.php')) {
    require_once __DIR__ . '/vendor_autoload.php';
}
require_once __DIR__ . '/../lib/internal/Magento/Framework/Autoload/IncludePath.php';
spl_autoload_register([new \Magento\Framework\Autoload\IncludePath(), 'load'], true, true);
