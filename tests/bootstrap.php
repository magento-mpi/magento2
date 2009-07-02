<?php
//var_dump(get_defined_vars());
//try {
//    throw new Exception();
//} catch (Exception $e) {
//    echo $e;
//}
//die();
if (!defined('MAGENTO_TESTS_BOOTSTAP')) {
    define('MAGENTO_TESTS_BOOTSTAP', true);
    $currentDir = realpath(dirname(__FILE__));

    require_once $currentDir . DIRECTORY_SEPARATOR . 'Mage.php';
    require_once $currentDir . DIRECTORY_SEPARATOR . 'TestSuite.php';
    require_once $currentDir . DIRECTORY_SEPARATOR . 'TestCase.php';

    set_include_path(get_include_path() . PS . dirname(__FILE__));

    Mage::app();

    restore_error_handler();

    class MageTest
    {
        /**
         * Run a single test case, test suite or AllTests package
         * Will call exit()
         *
         * @param string $filename
         */
        public static function run($path)
        {
            $class      = self::getPathClass($path);
            $isTestCase = (bool)preg_match('/Test$/', $class);
            $isAllTests = $isTestCase || (bool)preg_match('/AllTests$/', $class);
            if ($isTestCase || $isAllTests) {
                $suite = new Mage_PHPUnit_TestSuite();
                if ($isTestCase) {
                    $suite->addTestSuite($class);
                } elseif ($isAllTests) {
                    eval('$suite->addTest(' . $class . '::suite());');
                }
                PHPUnit_TextUI_TestRunner::run($suite);
            }
            exit;
        }

        /**
         * Build classname from filename
         * This class filename is assumed as top-level
         *
         * @param string $filename
         * @return string
         */
        protected static function getPathClass($filename)
        {
            $remove    = str_replace(basename(__FILE__), '', __FILE__);
            $classname = str_replace($remove, '', $filename);
            $classname = str_replace('.php', '', $classname);
            return preg_replace('/[^a-z]+/i', '_', $classname);
        }
    }

    /**
     * Run Tests
     * $filename variable comes from PHPUnit_Util_Fileloader::load environment
     */
     if (isset($filename)) {
         MageTest::run($filename);
     } else {
         echo "No test suite/case was specified \n";
         exit();
     }
}