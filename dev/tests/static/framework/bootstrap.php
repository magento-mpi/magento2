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

define('DS', DIRECTORY_SEPARATOR);
define('BP', realpath(__DIR__ . '/../../../../'));
require BP . '/app/autoload.php';
\Magento\Autoload\IncludePath::addIncludePath(array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    BP . '/lib',
));
\Magento\TestFramework\Utility\Files::init(new \Magento\TestFramework\Utility\Files(BP));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DS, $className);
    $filePath = BP . DS . 'dev' . DS . 'tools' . DS . $filePath . '.php';

    if (file_exists($filePath)) {
        include_once($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
