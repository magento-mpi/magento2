<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once('Mage.php');
require_once('MageTest.php');
Mage::init();


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
        $suite->addTest(Varien_AllTests::suite());
        $suite->addTestSuite('MageTest');
        $suite->addTest(Mage_AllTests::suite());
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}