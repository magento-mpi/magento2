<?php
if (!defined('_IS_INCLUDED')) {
    define('_IS_INCLUDED', 1);
    // setup include path and autoloader
    require realpath(dirname(__FILE__) . '/../app/Mage.php');
    set_include_path(get_include_path() . PS . dirname(__FILE__));

    // prevent error "headers already sent" for controllers
    session_start();
    Mage::$headersSentThrowsException = false;

    /**
     * Auto-runner for files, called directly, not via phpunit shell
     *
     */
    class PHPUnitTestInit
    {
        /**
         * Run a single test case, test suite or AllTests package
         * Will call exit()
         *
         * @param string $filename
         */
        public static function runMe($filename)
        {
            $class      = self::_filename2Classname($filename);
            $isTestCase = (bool)preg_match('/Test$/', $class);
            $isAllTests = $isTestCase || (bool)preg_match('/AllTests$/', $class);

            if ($isTestCase || $isAllTests) {
                $suite = new PHPUnit_Framework_TestSuite();
                // prepare test case
                if ($isTestCase) {
                    $suite->addTestSuite($class);
                }
                // prepare AllTests pack
                elseif ($isAllTests) {
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
        protected static function _filename2Classname($filename)
        {
            $remove    = str_replace(basename(__FILE__), '', __FILE__);
            $classname = str_replace($remove, '', $filename);
            $classname = str_replace('.php', '', $classname);
            return preg_replace('/[^a-z]+/i', '_', $classname);
        }
    }
}
