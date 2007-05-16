<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mage_Core_AllTests::main');
}

class Mage_Core_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage Core');
        //$suite->addTestSuite('MageTest');
        $suite->addTest(Mage_Core_Model_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mage_Core_AllTests::main') {
    Mage_Core_AllTests::main();
}