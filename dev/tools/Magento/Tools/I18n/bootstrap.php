<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
define('BP', realpath(__DIR__) . '/');

function i18n_tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') !== false) {
        $filePath = str_replace('\\', '/', str_replace('Magento\\Tools\\I18n\\', '', $className));
        $filePath = BP . $filePath . '.php';
    } else if (strpos($className, 'Zend_') !== false) {
        $filePath = BP . str_replace('_', '/', $className) . '.php';
    }
    if (isset($filePath) && file_exists($filePath)) {
        include_once($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('i18n_tool_autoloader');
