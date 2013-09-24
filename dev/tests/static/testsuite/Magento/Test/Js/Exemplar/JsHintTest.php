<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test composed JsHint test.
 * Used to ensure, that Magento coding standard rules (sniffs) really do what they are intended to do.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 */
namespace Magento\Test\Js\Exemplar;

class JsHintTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Inspection\JsHint\Command
     */
    protected static $_cmd = null;

    public static function setUpBeforeClass()
    {
        $reportFile = __DIR__ . '/../../../tmp/js_report.txt';
        $fileName = BP . '/pub/lib/mage/mage.js';
        self::$_cmd = new Magento_TestFramework_Inspection_JsHint_Command($fileName, $reportFile);
    }

    protected function setUp()
    {
        $reportFile = self::$_cmd->getReportFile();
        if (!is_dir(dirname($reportFile))) {
            mkdir(dirname($reportFile), 0777);
        }
    }

    protected function tearDown()
    {
        $reportFile = self::$_cmd->getReportFile();
        if (file_exists($reportFile)) {
            unlink($reportFile);
        }
        rmdir(dirname($reportFile));
    }

    public function testCanRun()
    {
        $result = false;
        try {
            $result = self::$_cmd->canRun();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue($result, true);
    }
}
