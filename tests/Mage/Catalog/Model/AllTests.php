<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mage_Catalog_Model_AllTests::main');
}

require_once('Mage/Catalog/Model/ProductTest.php');
require_once('Mage/Catalog/Model/CategoryTest.php');

class Mage_Catalog_Model_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage Catalog Model');
        $suite->addTestSuite('Mage_Catalog_Model_ProductTest');
        $suite->addTestSuite('Mage_Catalog_Model_CategoryTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mage_Catalog_Model_AllTests::main') {
    MAge_AllTests::main();
}