<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('BP', str_replace('\\', '/', realpath(__DIR__ . '/../../../../')));
require BP . '/app/autoload.php';
\Magento\Autoload\IncludePath::addIncludePath(array(__DIR__, dirname(__DIR__) . '/testsuite', BP . '/lib/internal'));
\Magento\TestFramework\Utility\Files::setInstance(new \Magento\TestFramework\Utility\Files(BP));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', '/', $className);
    $filePath = BP . '/dev/tools/' . $filePath . '.php';

    if (file_exists($filePath)) {
        include_once $filePath;
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
