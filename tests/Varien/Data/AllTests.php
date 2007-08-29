<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   default
 * @package    Tests_Varien
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mage_Catalog_Model_AllTests::main');
}

require_once('Varien/Data/CollectionTest.php');
require_once('Varien/Data/FormTest.php');
require_once('Varien/Data/TreeTest.php');

class Varien_Data_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Varien Data');
        $suite->addTestSuite('Varien_Data_CollectionTest');
        $suite->addTestSuite('Varien_Data_FormTest');
        $suite->addTestSuite('Varien_Data_TreeTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mage_Catalog_Model_AllTests::main') {
    MAge_AllTests::main();
}