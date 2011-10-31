<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
