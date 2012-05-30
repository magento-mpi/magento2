<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */
define('DS', DIRECTORY_SEPARATOR);
define('BP', dirname(__DIR__));

/* Initialize DEV constants */
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
date_default_timezone_set('America/Los_Angeles');

define('UNIT_ROOT', DEV_ROOT . '/dev/api-tests/unit');
define('UNIT_FRAMEWORK', UNIT_ROOT . '/framework');
define('UNIT_TEMP', UNIT_ROOT . '/tmp');

if (file_exists(UNIT_FRAMEWORK . '/config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.php.dist';
}
//require_once DEV_APP . '/Mage.php';
require_once DEV_APP . '/code/core/Mage/Core/functions.php';

$includePaths = array(
    realpath('./testsuite'),
    realpath(DEV_LIB),
    realpath(DEV_APP . '/code/core'),
    realpath(DEV_APP),
    realpath(UNIT_FRAMEWORK),
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));
spl_autoload_register('magentoAutoloadForUnitTests');
register_shutdown_function('magentoCleanTmpForUnitTests');

chdir(DEV_ROOT);
//need to initialize test App configuration in bootstrap
//because data providers in test cases are run before setUp() and even before setUpBeforeClass() methods in TestCase.
Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_App')->run();


function magentoAutoloadForUnitTests($class)
{
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        $fileName = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fileName)) {
            include $file;
        }
    }
    return false;
}

function magentoCleanTmpForUnitTests()
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(TESTS_TEMP_DIR),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        if (strpos($file->getFilename(), '.') === 0) {
            continue;
        }
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}
