<?php
/**
 * A script that instantiates and returns autoloader for further reusing
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once __DIR__ . '/../../lib/Magento/Autoload/IncludePath.php';
$loader = new Magento_Autoload_IncludePath;
spl_autoload_register(array($loader, 'autoload'));
return $loader;
