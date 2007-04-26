<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mage_AllTests::main');
}


class Mage_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage');
        //$suite->addTest(Mage_AllTests::suite());
        $suite->addTest(Mage_Catalog_AllTests::suite());
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mage_AllTests::main') {
    MAge_AllTests::main();
}