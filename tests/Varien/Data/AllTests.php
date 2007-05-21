<?php
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