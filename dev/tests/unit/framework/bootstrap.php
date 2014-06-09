<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!defined('TESTS_TEMP_DIR')) {
    define('BP', realpath(__DIR__ . '/../../../../'));
    define('TESTS_TEMP_DIR', dirname(__DIR__) . '/tmp');
}

require BP . '/app/functions.php';
require BP . '/app/autoload.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
    array(
        __DIR__,
        realpath(__DIR__ . '/../testsuite'),
        realpath(BP . '/app'),
        realpath(BP . '/app/code'),
        realpath(BP . '/lib')
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
    //Adding ability for Composer Tool
    if(strpos($className, 'Magento\\Composer\\') !== false){
         $filePath = str_replace('\\', '/', $className);
        $composerPath = BP. '/dev/tools/composer-packager/'.$filePath.'.php';
        if (file_exists($composerPath)) {
            include_once $composerPath;

        }
    }
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
