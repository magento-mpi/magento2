<?php

require_once 'PHPUnit/Framework.php';

require_once 'Mage.php';
require_once 'Fixture.php';
require_once 'DbAdapter.php';
require_once 'TestConstraints.php';
require_once 'TestCase.php';
require_once 'TestSuite.php';

/**
 * Test runner for available UnitTests
 */
class AllTests extends Mage_TestSuite
{
    /**
     * Base Scan folders contains TestCases and TestSuits
     *
     * @var array
     */
    protected static $_baseTestFolders = array('bugs', 'functional',
        'integration', 'lib','modules', 'selenium', 'webservices');

    /**
     * Retrieve Main Suite
     *
     * @return Mage_TestSuite
     */
    public static function suite()
    {
        // initialize application before collect tests
        self::runApp();

        $suite = new Mage_TestSuite('Magento ver. ' . Mage::getVersion());
        foreach (self::$_baseTestFolders as $folder) {
            $path = dirname(__FILE__) . DS . $folder;
            self::_findTests($suite, $path);
        }

        return $suite;
    }

    /**
     * Initialize application
     *
     */
    public static function runApp()
    {
        // emulate session_start process
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = array();
        }

        // disable cache
        $serFileOld = BP . DS . 'app' . DS . 'etc' . DS . 'use_cache.ser';
        $serFileNew = BP . DS . 'app' . DS . 'etc' . DS . 'use_cache.bac';
        $serFileEnv = BP . DS . 'tests' . DS . 'use_cache.ser';

        if (file_exists($serFileOld)) {
            rename($serFileOld, $serFileNew);
        }
        copy($serFileEnv, $serFileOld);

        Mage::app();

        // register db adapter for fixtures
        Mage::register('_dbadapter', new Mage_DbAdapter());
        Mage::register('_fixture', new Mage_Fixture());

        // restore original cache settings
        unlink($serFileOld);
        if (file_exists($serFileNew)) {
            rename($serFileNew, $serFileOld);
        }
    }
}
