<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mage_Core_Model_AllTests::main');
}

require_once('Mage/Core/Model/LayoutTest.php');

class Mage_Core_Model_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage Core Models');
        $suite->addTestSuite('Mage_Core_Model_LayoutTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mage_Core_Model_AllTests::main') {
    Mage_Core_Model_AllTests::main();
}