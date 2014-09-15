<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Php;

use Magento\TestFramework\CodingStandard\Tool\CodeMessDetector;
use Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper;
use Magento\TestFramework\CodingStandard\Tool\CodeSniffer;
use Magento\TestFramework\CodingStandard\Tool\CopyPasteDetector;
use Magento\TestFramework\Utility;
use PHP_PMD_TextUI_Command;
use PHPUnit_Framework_TestCase;

/**
 * Set of tests for static code analysis, e.g. code style, code complexity, copy paste detecting, etc.
 */
class LiveCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $reportDir = '';

    /**
     * @var string
     */
    protected static $pathToSource = '';

    /**
     * Setup basics for all tests
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$pathToSource = Utility\Files::init()->getPathToSource();
        self::$reportDir = self::$pathToSource . '/dev/tests/static/report';
        if (!is_dir(self::$reportDir)) {
            mkdir(self::$reportDir, 0777);
        }
    }

    /**
     * Returns whitelist based on blacklist and git changed files
     *
     * @param array $fileTypes
     * @return array
     */
    public static function getWhitelist($fileTypes = ['php'])
    {
        $directoriesToCheck = file(__DIR__ . '/_files/whitelist/whitelist.txt', FILE_IGNORE_NEW_LINES);

        $changedFiles = array_filter(
            Utility\Files::readLists(__DIR__ . '/_files/changed_files.txt'),
            function ($path) use ($directoriesToCheck) {
                foreach ($directoriesToCheck as $directory) {
                    if (strpos($path, BP . '/' . $directory) === 0) {
                        return true;
                    }
                }
                return false;
            }
        );
        if (!empty($fileTypes)) {
            $changedFiles = array_filter(
                $changedFiles,
                function ($path) use ($fileTypes) {
                    return in_array(pathinfo($path, PATHINFO_EXTENSION), $fileTypes);
                }
            );
        }
        
        return $changedFiles;
    }

    /**
     * Run the PSR2 code sniffs on the code
     *
     * @TODO: combine with testCodeStyle
     * @return void
     */
    public function testCodeStylePsr2()
    {
        $reportFile = self::$reportDir . '/phpcs_psr2_report.xml';
        $wrapper = new Wrapper();
        $codeSniffer = new CodeSniffer('PSR2', $reportFile, $wrapper);
        if (!$codeSniffer->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }
        if (version_compare($wrapper->version(), '1.4.7') === -1) {
            $this->markTestSkipped('PHP Code Sniffer Build Too Old.');
        }

        $result = $codeSniffer->run(self::getWhitelist());

        $this->assertEquals(
            0,
            $result,
            "PHP Code Sniffer has found {$result} error(s): See detailed report in {$reportFile}"
        );
    }

    /**
     * Run the magento specific coding standards on the code
     *
     * @return void
     */
    public function testCodeStyle()
    {
        $reportFile = self::$reportDir . '/phpcs_report.xml';
        $wrapper = new Wrapper();
        $codeSniffer = new CodeSniffer(realpath(__DIR__ . '/_files/phpcs'), $reportFile, $wrapper);
        if (!$codeSniffer->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }
        $codeSniffer->setExtensions(['php', 'phtml']);
        $result = $codeSniffer->run(self::getWhitelist(['php', 'phtml']));
        $this->assertEquals(
            0,
            $result,
            "PHP Code Sniffer has found {$result} error(s): See detailed report in {$reportFile}"
        );
    }

    /**
     * Run the annotations sniffs on the code
     *
     * @return void
     * @todo Combine with normal code style at some point.
     */
    public function testAnnotationStandard()
    {
        $reportFile = self::$reportDir . '/phpcs_annotations_report.xml';
        $wrapper = new Wrapper();
        $codeSniffer = new CodeSniffer(
            realpath(__DIR__ . '/../../../../framework/Magento/ruleset.xml'),
            $reportFile,
            $wrapper
        );
        if (!$codeSniffer->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }

        $this->assertEquals(
            0,
            $result = $codeSniffer->run(self::getWhitelist(['php'])),
            "PHP Code Sniffer has found {$result} error(s): See detailed report in {$reportFile}"
        );
    }

    /**
     * Run mess detector on code
     */
    public function testCodeMess()
    {
        $reportFile = self::$reportDir . '/phpmd_report.xml';
        $codeMessDetector = new CodeMessDetector(realpath(__DIR__ . '/_files/phpmd/ruleset.xml'), $reportFile);
        if (!$codeMessDetector->canRun()) {
            $this->markTestSkipped('PHP Mess Detector is not available.');
        }

        $this->assertEquals(
            PHP_PMD_TextUI_Command::EXIT_SUCCESS,
            $codeMessDetector->run(self::getWhitelist(['php'])),
            "PHP Code Mess has found error(s): See detailed report in {$reportFile}"
        );

        // delete empty reports
        if (file_exists($reportFile)) {
            unlink($reportFile);
        }
    }

    /**
     * Run copy paste detector on code
     *
     * @return void
    */
    public function testCopyPaste()
    {
        $reportFile = self::$reportDir . '/phpcpd_report.xml';
        $copyPasteDetector = new CopyPasteDetector($reportFile);

        if (!$copyPasteDetector->canRun()) {
            $this->markTestSkipped('PHP Copy/Paste Detector is not available.');
        }

        $blackList = array();
        foreach (glob(__DIR__ . '/_files/phpcpd/blacklist/*.txt') as $list) {
            $blackList = array_merge($blackList, file($list, FILE_IGNORE_NEW_LINES));
        }
        $copyPasteDetector->setBlackList($blackList);

        $this->assertTrue(
            $copyPasteDetector->run([BP]),
            "PHP Copy/Paste Detector has found error(s): See detailed report in {$reportFile}"
        );
    }
}
