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
 * TestSuite main class
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * The pattern for TestCase files
     *
     * @var string
     */
    protected static $_caseFileMask = '*Test.php';

    /**
     * Find TestCases by path and add to Base TestSuite
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @param string $path
     */
    protected static function _findTests(PHPUnit_Framework_TestSuite $suite, $path)
    {
        // check exists TestSuite
        $path = rtrim($path, DS) . DS;

        // processing current directories
        $dirs = glob($path . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dirPath) {
            self::_findTests($suite, $dirPath);
        }

        // find and add test cases
        $cases = glob($path . self::$_caseFileMask);
        $suite->addTestFiles($cases);
    }

    /**
     * Initialize application
     */
    public static function runApp()
    {
        // emulate session_start process
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = array();
        }
    }
}
