<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Script which helps to debug needed test case from IDE.
 * To debug needed TestCase file you should set your test path in
 * setTestingFilePath() function call and start debugging this file.
 * 'phpunit' command emulator.
 */

/**
 * Function which sets needed data to allow testCase debugging
 *
 * @param string $path full path to testCase or testSuite file needed to debug
 */
function setTestingFilePath($path)
{
    $_SERVER = array(
        'argv' => array(
            0 => '',
            1 => $path
        ),
        'argc' => 2
    );
}

//set your full path to your needed file to debug
setTestingFilePath('E:\internal\magentocommerce\products\tests\unit\tests\AppStore\Extension\Model\ProductTest.php');


require_once 'PHP/CodeCoverage/Filter.php';
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUNIT');

if (extension_loaded('xdebug')) {
    xdebug_disable();
}

require_once 'PHPUnit/Autoload.php';

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

PHPUnit_TextUI_Command::main();
