<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once __DIR__ . '/../lib/Magento/Autoload/Loader.php';
require_once __DIR__ . '/../lib/Magento/Autoload/IncludePath.php';
$loader = new Magento_Autoload_Loader('Magento_Autoload_IncludePath::getFile');
$loader->register();
