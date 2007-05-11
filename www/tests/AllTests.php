<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require 'Mage.php';
//Mage::initAdmin();

require_once 'config.php';
set_include_path(dirname(__FILE__) . PATH_SEPARATOR .  get_include_path());

class AllTests
{
    public static function main()
    {
        $parameters = array();
        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento');
        //$suite->addTest(Mage_AllTests::suite());
        $suite->addTest(Varien_AllTests::suite());
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}