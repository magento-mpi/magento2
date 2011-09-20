<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @subpackage  batch_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that executes all test suites in Magento
 */
class Batch
{
    /**
     * Test suites configuration, loaded from config file
     *
     * @var array
     */
    protected $_tests;

    /**
     * Directory for report paths
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * Runs all test suites
     *
     * @param  string $dir
     * @param  string $title
     * @param  string $reportFile
     * @return Batch
     */
    public function run()
    {
        $this->_loadConfig();

        $this->_log();
        $numFailures = 0;
        $numPassed = 0;
        foreach ($this->_tests as $test) {
            $result = $this->_runTestSuite($test['dir'], $test['report_file']);
            if ($result['errorMessage']) {
                $this->_log("{$test['title']} - Failed.");
                $this->_log("    {$result['errorMessage']}");
                $numFailures++;
            } else {
                $this->_log("{$test['title']} - Ok.");
                $numPassed++;
            }
        }

        $this->_log(' ');
        if ($numFailures) {
            $total = $numPassed + $numFailures;
            $this->_log("FAILED. Total test suites: {$total}, failures: {$numFailures}, passed: {$numPassed}.");
        } else {
            $this->_log("PASSED. Total test suites: {$numPassed}.");
        }

        return $this;
    }

    /**
     * Loads xml config, stores it in internal properties
     *
     * @return Batch
     */
    protected function _loadConfig()
    {
        $fileName = $this->_getConfigFileName();
        $xml = new SimpleXMLElement(file_get_contents($fileName));

        // Load general options
        $reportDir = null;
        $reportDirNode = $xml->xpath('/config/general/report_dir');
        if (isset($reportDirNode[0])) {
            $reportDir = (string) $reportDirNode[0];
        }
        if ($reportDir === null) {
            $reportDir = 'report';
        }
        $reportDir = __DIR__ . '/../' . $reportDir;
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
        $this->_reportDir = realpath($reportDir);

        // Load tests
        $tests = array();
        $testNodes = $xml->xpath('/config/tests/test');
        foreach ($testNodes as $test) {
            $attributes = $test->attributes();
            if (!isset($attributes->dir)) {
                throw new Exception('No directory is defined for a test.');
            }
            $dir = realpath($attributes->dir);
            if (!is_dir($dir)) {
                throw new Exception('Directory with tests "' . $dir . '" not found.');
            }

            $title = $dir;
            if (isset($attributes->title)) {
                $title = (string) $attributes->title;
            }

            $reportFile = (string) $attributes->report;
            if (!strlen($reportFile)) {
                throw new Exception('No report file is defined for a test');
            }

            $tests[] = array(
                'dir' => $dir,
                'title' => $title,
                'report_file' => $reportFile
            );
        }
        $this->_tests = $tests;

        return $this;
    }

     /**
      * Returns a filename with config
      *
      * @return string
      */
    protected function _getConfigFileName()
    {
        $configs = array('config.xml', 'config.xml.dist');
        $result = null;
        foreach ($configs as $config) {
            $checkFileName = __DIR__ . '/../etc/' . $config;
            if (file_exists($checkFileName)) {
                $result = $checkFileName;
                break;
            }
        }

        if (!$result) {
            throw new Exception('Config file does not exist.');
        }

        return $result;
    }

    /**
     * Runs whole test suite and returns result
     *
     * @param  string $dir
     * @param  string $reportFile
     * @return array
     */
    protected function _runTestSuite($dir, $reportFile)
    {
        $reportFileFull = $this->_reportDir . '/' . $reportFile;
        if (file_exists($reportFileFull)) {
            unlink($reportFileFull);
        }

        chdir($dir);
        exec('phpunit', $output, $returnVal);

        $output = implode("\n", $output);
        file_put_contents($reportFileFull, $output);

        // Analyze return code
        $result = array(
            'output' => $output,
            'errorMessage' => null
        );
        try {
            // Analyze exit code
            if ($returnVal != 0) {
                throw new Exception('Error: failed.');
            }
            // Analyze output for unexpected termination - e.g. "Time: 2 seconds, Memory: 11.25Mb" must be there
            if (strpos($output, ', Memory:') === false) {
                throw new Exception('Error: unknown result.');
            }
        } catch (Exception $e) {
            $result['errorMessage'] = $e->getMessage() . ' See report in ' . realpath($reportFileFull);
        }

        return $result;
    }

    /**
     * Outputs string to the terminal.
     *
     * @param string|null $message
     * @return Batch
     */
    protected function _log($message = null)
    {
        echo $message, "\n";
        return $this;
    }
}
