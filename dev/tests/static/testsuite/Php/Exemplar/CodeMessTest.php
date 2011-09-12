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

class Php_Exemplar_CodeMessTest extends PHPUnit_Framework_TestCase
{
    protected $_actualReportFile;

    protected function setUp()
    {
        if (!$this->_actualReportFile) {
            $this->_actualReportFile = __DIR__ . '/../../../tmp/phpmd_report.xml';
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
     * @dataProvider ruleViolationDataProvider
     */
    public function testRuleViolation($inputFile, $expectedReportFile, $minRequiredVersion = null)
    {
        $cmd = new Inspection_MessDetector_Command(
            realpath(__DIR__ . '/../_files/phpmd/ruleset.xml'),
            $this->_actualReportFile,
            array($inputFile)
        );
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Mess Detector command line is not available.');
        }
        if ($minRequiredVersion && version_compare($cmd->getVersion(), $minRequiredVersion, '<')) {
            $this->markTestSkipped("PHP Mess Detector minimal required version is {$minRequiredVersion}.");
        }
        $this->assertFileNotExists($this->_actualReportFile);
        $this->assertFalse($cmd->run(), 'Command should end up with an error.');
        $this->assertFileExists($this->_actualReportFile);

        /* Cleanup report from the variable information */
        $actualReportXml = file_get_contents($this->_actualReportFile);
        $actualReportXml = preg_replace('/(?<!\?xml)\s+version=".+?"/', '', $actualReportXml, 1);
        $actualReportXml = preg_replace('/\s+(?:timestamp|externalInfoUrl)=".+?"/', '', $actualReportXml);
        $actualReportXml = str_replace(realpath($inputFile), basename($inputFile), $actualReportXml);

        $this->assertXmlStringEqualsXmlFile($expectedReportFile, $actualReportXml);
    }

    public function ruleViolationDataProvider()
    {
        return array(
            'cyclomatic complexity' => array(
                __DIR__ . '/_files/phpmd/input/cyclomatic_complexity.php',
                __DIR__ . '/_files/phpmd/output/cyclomatic_complexity.xml',
            ),
            'method length' => array(
                __DIR__ . '/_files/phpmd/input/method_length.php',
                __DIR__ . '/_files/phpmd/output/method_length.xml',
            ),
            'parameter list' => array(
                __DIR__ . '/_files/phpmd/input/parameter_list.php',
                __DIR__ . '/_files/phpmd/output/parameter_list.xml',
            ),
            'method count' => array(
                __DIR__ . '/_files/phpmd/input/method_count.php',
                __DIR__ . '/_files/phpmd/output/method_count.xml',
            ),
            'field count' => array(
                __DIR__ . '/_files/phpmd/input/field_count.php',
                __DIR__ . '/_files/phpmd/output/field_count.xml',
            ),
            'public count' => array(
                __DIR__ . '/_files/phpmd/input/public_count.php',
                __DIR__ . '/_files/phpmd/output/public_count.xml',
            ),
            'prohibited statement' => array(
                __DIR__ . '/_files/phpmd/input/prohibited_statement.php',
                __DIR__ . '/_files/phpmd/output/prohibited_statement.xml',
            ),
            'prohibited statement goto' => array(
                __DIR__ . '/_files/phpmd/input/prohibited_statement_goto.php',
                __DIR__ . '/_files/phpmd/output/prohibited_statement_goto.xml',
                '1.1.0',
            ),
            'inheritance depth' => array(
                __DIR__ . '/_files/phpmd/input/inheritance_depth.php',
                __DIR__ . '/_files/phpmd/output/inheritance_depth.xml',
            ),
            'descendant count' => array(
                __DIR__ . '/_files/phpmd/input/descendant_count.php',
                __DIR__ . '/_files/phpmd/output/descendant_count.xml',
            ),
            'coupling' => array(
                __DIR__ . '/_files/phpmd/input/coupling.php',
                __DIR__ . '/_files/phpmd/output/coupling.xml',
                '1.1.0',
            ),
            'naming' => array(
                __DIR__ . '/_files/phpmd/input/naming.php',
                __DIR__ . '/_files/phpmd/output/naming.xml',
            ),
            'unused' => array(
                __DIR__ . '/_files/phpmd/input/unused.php',
                __DIR__ . '/_files/phpmd/output/unused.xml',
            ),
        );
    }
}
