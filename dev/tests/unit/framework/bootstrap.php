<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', dirname(__DIR__) . '/tmp');
}
require __DIR__ . '/../../../../app/autoload.php';

$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);

require BP . '/app/functions.php';
$includePath->addIncludePath(
    array(
        __DIR__,
        realpath(__DIR__ . '/../testsuite'),
        realpath(BP . '/app'),
        realpath(BP . '/app/code'),
        realpath(BP . '/lib/internal')
    )
);

if (is_dir(TESTS_TEMP_DIR)) {
    $filesystemAdapter = new \Magento\Framework\Filesystem\Driver\File();
    $filesystemAdapter->deleteDirectory(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

\Magento\Framework\Phrase::setRenderer(new \Magento\Framework\Phrase\Renderer\Placeholder());

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
error_reporting(E_ALL);
ini_set('display_errors', 1);
