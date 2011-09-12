<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test composed Magento coding standard against different code cases.
 * Used to ensure, that Magento coding standard rules (sniffs) really do what they are intended to do.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 */
class Php_Exemplar_CodeStyleTest extends PHPUnit_Framework_TestCase
{
    protected $_actualReportFile;

    protected function setUp()
    {
        if (!$this->_actualReportFile) {
            $this->_actualReportFile = __DIR__ . '/../../../tmp/phpcs_report.xml';
            $dirname = dirname($this->_actualReportFile);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }
        }
    }

    protected function tearDown()
    {
        if (file_exists($this->_actualReportFile)) {
            unlink($this->_actualReportFile);
        }
    }

    /**
     * @dataProvider ruleDataProvider
     */
    public function testRule($inputFile, $expectedConfigFile)
    {
        // Load expectation
        if (!file_exists($expectedConfigFile)) {
            $this->fail('Expectation config file "' . $expectedConfigFile . '" does not exist.');
        }
        $expected = new SimpleXMLElement(file_get_contents($expectedConfigFile));

        // Test should be skipped (rule is not implemented)
        $elements = $expected->xpath('/config/skipped');
        if ($elements) {
            $message = (string) $elements[0];
            $this->markTestSkipped('Skipped testing ' . $inputFile . ' - ' . $message);
        }

        // Check to run additional methods before making test
        $elements = $expected->xpath('/config/run');
        foreach ($elements as $element) {
            $method = (string) $element->attributes()->method;
            $this->$method();
        }

        // Process input file
        $cmd = new Inspection_CodeSniffer_Command(
            realpath(__DIR__ . '/../_files/phpcs'),
            $this->_actualReportFile,
            array($inputFile)
        );
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer command line is not available.');
        }
        $cmd->run();
        $this->assertFileExists($this->_actualReportFile);

        $report = new SimpleXMLElement(file_get_contents($this->_actualReportFile));
        $this->_checkReportAgainstExpectations($report, $expected);

        // Check maybe we did nothing, just checked report existance.
        if ($this->getCount() == 1) {
            $this->fail('Wrong test config, nothing is tested in ' . $inputFile);
        }
    }

    /**
     * @return array
     */
    public function ruleDataProvider()
    {
        $inputDir = __DIR__ . '/_files/phpcs/input/';
        $expectationDir = __DIR__ . '/_files/phpcs/expected/';
        return $this->_getTestsAndExpectations($inputDir, $expectationDir);
    }

    /**
     * Recursively searches paths and adds files and expectations to the list of fixtures for tests
     *
     * @param string $inputDir
     * @param string $expectationDir
     * @return array
     */
    protected function _getTestsAndExpectations($inputDir, $expectationDir)
    {
        $result = array();
        $skipFiles = array('.', '..', '.svn');
        $dir = dir($inputDir);
        do {
            $file = $dir->read();
            if (($file === false) || in_array($file, $skipFiles)) {
                continue;
            }

            $inputFilePath = $inputDir . $file;
            $expectationFilePath = $expectationDir . $file;

            if (is_dir($inputFilePath)) {
                $more = $this->_getTestsAndExpectations($inputFilePath . '/', $expectationFilePath . '/');
                $result = array_merge($result, $more);
                continue;
            }

            $pathinfo = pathinfo($inputFilePath);
            $expectationFilePath = $expectationDir . $pathinfo['filename'] . '.xml';
            $result[] = array($inputFilePath, $expectationFilePath);
        } while ($file !== false);
        $dir->close();

        return $result;
    }

    /**
     * Checks report against expectations, by issuing several 'assert' statements
     *
     * @param SimpleXMLElement $report
     * @param SimpleXMLElement $expected
     * @return Php_Etalon_CodeStyleTest
     */
    protected function _checkReportAgainstExpectations($report, $expected)
    {
        // a) Total errors and warnings
        $elements = $expected->xpath('/config/total');
        if ($elements) {
            $numErrorsActual = count($report->xpath('/checkstyle/file/error[@severity="error"]'));
            $numWarningsActual = count($report->xpath('/checkstyle/file/error[@severity="warning"]'));

            $element = $elements[0];
            $attributes = $element->attributes();
            if (isset($attributes->errors)) {
                $numErrorsExpected = (string) $attributes->errors;
                $this->assertEquals(
                    $numErrorsExpected,
                    $numErrorsActual,
                    'Expecting ' . $numErrorsExpected . ' errors, got ' . $numErrorsActual
                );
            }
            if (isset($attributes->warnings)) {
                $numWarningsExpected = (string) $attributes->warnings;
                $this->assertEquals(
                    $numWarningsExpected,
                    $numWarningsActual,
                    'Expecting ' . $numWarningsExpected . ' warnings, got ' . $numWarningsActual
                );
            }
        }

        // b) Errors
        $elements = $expected->xpath('/config/error');
        foreach ($elements as $element) {
            $lineExpected = (string) $element->attributes()->line;
            $errorElement = $report->xpath('/checkstyle/file/error[@severity="error"][@line=' . $lineExpected . ']');
            $this->assertNotEmpty(
                $errorElement,
                'Expected error at line ' . $lineExpected . ' is not detected by PHPCS.'
            );
        }

        // c) Warnings
        $elements = $expected->xpath('/config/warning');
        foreach ($elements as $element) {
            $lineExpected = (string) $element->attributes()->line;
            $errorElement = $report->xpath('/checkstyle/file/error[@severity="warning"][@line=' . $lineExpected . ']');
            $this->assertNotEmpty(
                $errorElement,
                'Expected warning at line ' . $lineExpected . ' is not detected by PHPCS.'
            );
        }

        return $this;
    }

    /**
     * Checks, whether short open tags are allowed.
     * Check-method, used by test-configs and executed before executing tests.
     *
     * @return null
     */
    protected function _checkShortTagsOn()
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('"short_open_tag" setting must be set to "On" to test this case.');
        }
    }

    /**
     * Checks, whether short open tags in ASP-style are allowed.
     * Check-method, used by test-configs and executed before executing tests.
     *
     * @return null
     */
    protected function _checkAspTagsOn()
    {
        if (!ini_get('asp_tags')) {
            $this->markTestSkipped('"asp tags" setting must be set to "On" to test this case.');
        }
    }
}
