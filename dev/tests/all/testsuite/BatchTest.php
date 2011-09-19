<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  all_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that executes all test suites in Magento
 */
class BatchTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test suites configuration, loaded from config file
     *
     * @var array
     */
    protected static $_tests;

    /**
     * Directory for report paths
     *
     * @var string
     */
    protected static $_reportDir;

    /**
     *
     * @dataProvider suitesDataProvider
     *
     * @param  string $dir
     * @param  string $title
     * @param  string $reportFile
     * @return void
     */
    public function testSuites($dir, $title, $reportFile)
    {
        $reportFileFull = self::$_reportDir . '/' . $reportFile;
        if (file_exists($reportFileFull)) {
            unlink($reportFileFull);
        }

        chdir($dir);
        exec('phpunit', $output, $returnVal);

        $output = implode("\n", $output);
        file_put_contents($reportFileFull, $output);
        $reportFileFull = realpath($reportFileFull);

        // Analyze return code
        $this->assertEquals(0, $returnVal, $title . ' - failed. See report in ' . $reportFileFull);

        // Analyze output for unexpected termination - e.g. "Time: 2 seconds, Memory: 11.25Mb" must be there
        $this->assertContains(', Memory:', $output, $title . ' - unknown result. See report in ' . $reportFileFull);
    }

    /**
     * @return array
     */
    public function suitesDataProvider()
    {
        $this->_loadConfig();

        $result = array();
        foreach (self::$_tests as $test) {
            // Choose a human-readable key for data set
            $key = $test['title'];
            $i = 1;
            while (isset($result[$key])) {
                $i++;
                $key = $test['title'] . ' ' . $i;
            }

            // Compose data set
            $result[$key] = array($test['dir'], $test['title'], $test['report_file']);
        }

        return $result;
    }

    /**
     * Loads xml config, stores it in internal properties
     *
     * @return BatchTest
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
        self::$_reportDir = realpath($reportDir);

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
        self::$_tests = $tests;

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
}
