<?php

require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Configuration.php';
require_once 'PHPUnit/Util/Fileloader.php';
require_once 'PHPUnit/Util/Filesystem.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Getopt.php';

/**
 * The starter of the test framework
 *
 * @author Magento Inc.
 */
class TestStarter
{
    public static function run()
    {
        Arguments::init();

        $arguments = array();
        $tests = new PHPUnit_Framework_TestSuite;

        foreach (Arguments::$options as $testCase) {
            if (!$testCase) {
                continue;
            } elseif (false !== strpos($testCase, '.php')) {
                $tests->addTestFile($testCase);
            } else {
                $testCases = Core::getTestSuiteSource($testCase);

                if (false === strpos($testCases, ',')) {
                    $testCases = array($testCases);
                } else {
                    $testCases = preg_split('/\s*,\s*/', $testCases);
                }
                
                foreach ($testCases as $testCase) {
                    if (false !== strpos($testCase, '/')) {
                        list($testCase, $testMethodName) = explode('/', $testCase);
                        // echo "[Test: {$testMethodName}]\n";
                    } else {
                        $testMethodName = null;
                    }
                    // echo "[TestCase: {$testCase}]\n";

                    if ($testCase) {
                        if ($testMethodName) {
                            $arguments['filter'] = $testMethodName;
                        }
                    }

                    $tests->addTestSuite($testCase);
                }
            }
        }

        $runner = new PHPUnit_TextUI_TestRunner();
        $listener = new TestListener();

        $arguments['listeners'] = array($listener);

        try {
            $result = $runner->doRun($tests, $arguments);
        }

        catch (PHPUnit_Framework_Exception $e) {
            print "\n=======================================" . $e->getMessage() . "\n=======================================";
        }

        if (isset($result) && $result->wasSuccessful()) {
            exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
        }

        else if (!isset($result) || $result->errorCount() > 0) {
            exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
        }

        else {
            exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
        }
        
    }
}
