<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
define('BP', realpath(__DIR__ . '/../') . '/');

function i18n_tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') !== false) {
        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('Magento\\Tools\\I18n\\', '', $className));
        $filePath = BP . $filePath . '.php';
    } else if (strpos($className, 'Zend_') !== false) {
        $filePath = BP . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    }
    if (isset($filePath) && file_exists($filePath)) {
        include($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('i18n_tool_autoloader');
