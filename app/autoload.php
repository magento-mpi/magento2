<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once __DIR__ . '/../lib/Magento/Framework/Autoload/IncludePath.php';
spl_autoload_register('\Magento\Framework\Autoload\IncludePath::load');
