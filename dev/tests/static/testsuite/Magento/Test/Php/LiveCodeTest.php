<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Php;
use Magento\TestFramework\Utility;

/**
 * Set of tests for static code analysis, e.g. code style, code complexity, copy paste detecting, etc.
 */
class LiveCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_reportDir = '';

    /**
     * @var array
     */
    protected static $_whiteList = array();

    /**
     * @var array
     */
    protected static $_blackList = array();

    public static function setUpBeforeClass()
    {
        self::$_reportDir = Utility\Files::init()->getPathToSource()
            . '/dev/tests/static/report';
        if (!is_dir(self::$_reportDir)) {
            mkdir(self::$_reportDir, 0777);
        }
        self::setupFileLists();
    }

    public static function setupFileLists($type = '')
    {
        if ($type != '' && !preg_match('/\/$/', $type)) {
            $type = $type . '/';
        }
        self::$_whiteList = Utility\Files::readLists(__DIR__ . '/_files/' . $type . 'whitelist/*.txt');
        self::$_blackList = Utility\Files::readLists(__DIR__ . '/_files/' . $type . 'blacklist/*.txt');
    }

    /**
     * @TODO: combine with testCodeStyle
     */
    public function testCodeStylePsr2()
    {
        $reportFile = self::$_reportDir . '/phpcs_psr2_report.xml';
        $wrapper = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper();
        $codeSniffer = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer(
            'PSR2',
            $reportFile,
            $wrapper
        );
        if (!$codeSniffer->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }
        if (version_compare($codeSniffer->version(), '1.4.7') === -1) {
            $this->markTestSkipped('PHP Code Sniffer Build Too Old.');
        }
        self::setupFileLists('phpcs');
        $result = $codeSniffer->run(self::$_whiteList, self::$_blackList, array('php'));
        $this->assertFileExists(
            $reportFile,
            'Expected ' . $reportFile . ' to be created by phpcs run with PSR2 standard'
        );
        $this->markTestIncomplete("PHP Code Sniffer has found $result error(s): See detailed report in $reportFile");
        $this->assertEquals(
            0,
            $result,
            "PHP Code Sniffer has found $result error(s): See detailed report in $reportFile"
        );
    }

    public function testCodeStyle()
    {
        $reportFile = self::$_reportDir . '/phpcs_report.xml';
        $wrapper = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper();
        $codeSniffer = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer(
            realpath(__DIR__ . '/_files/phpcs'),
            $reportFile,
            $wrapper
        );
        if (!$codeSniffer->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }
        self::setupFileLists();
        $result = $codeSniffer->run(self::$_whiteList, self::$_blackList, array('php', 'phtml'));
        $this->assertEquals(
            0,
            $result,
            "PHP Code Sniffer has found $result error(s): See detailed report in $reportFile"
        );
    }

    public function testCodeMess()
    {
        $reportFile = self::$_reportDir . '/phpmd_report.xml';
        $codeMessDetector = new \Magento\TestFramework\CodingStandard\Tool\CodeMessDetector(
            realpath(__DIR__ . '/_files/phpmd/ruleset.xml'),
            $reportFile
        );

        if (!$codeMessDetector->canRun()) {
            $this->markTestSkipped('PHP Mess Detector is not available.');
        }

        self::setupFileLists();
        $this->assertEquals(
            \PHP_PMD_TextUI_Command::EXIT_SUCCESS,
            $codeMessDetector->run(self::$_whiteList, self::$_blackList),
            "PHP Code Mess has found error(s): See detailed report in $reportFile"
        );
    }

    public function testCopyPaste()
    {
        $reportFile = self::$_reportDir . '/phpcpd_report.xml';
        $copyPasteDetector = new \Magento\TestFramework\CodingStandard\Tool\CopyPasteDetector($reportFile);

        if (!$copyPasteDetector->canRun()) {
            $this->markTestSkipped('PHP Copy/Paste Detector is not available.');
        }

        self::setupFileLists();
        $blackList = array();
        foreach (glob(__DIR__ . '/_files/phpcpd/blacklist/*.txt') as $list) {
            $blackList = array_merge($blackList, file($list, FILE_IGNORE_NEW_LINES));
        }

        $this->assertTrue(
            $copyPasteDetector->run(array(), $blackList),
            "PHP Copy/Paste Detector has found error(s): See detailed report in $reportFile"
        );
    }
}
