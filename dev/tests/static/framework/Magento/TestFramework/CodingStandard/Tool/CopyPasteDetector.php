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
 * PHP Copy Paste Detector v1.4.0 tool wrapper
 */
class Magento_TestFramework_CodingStandard_Tool_CopyPasteDetector
    implements Magento_TestFramework_CodingStandard_ToolInterface
{
    /**
     * Report file
     *
     * @var string
     */
    protected $_reportFile;

    /**
     * Constructor
     *
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($reportFile)
    {
        $this->_reportFile = $reportFile;
    }

    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return (bool)@include 'SebastianBergmann/PHPCPD/autoload.php';
    }

    /**
     * Run tool for files specified
     *
     * @param array $whiteList Files/directories to be inspected
     * @param array $blackList Files/directories to be excluded from the inspection
     * @param array $extensions Array of alphanumeric strings, for example: 'php', 'xml', 'phtml', 'css'...
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return int
     */
    public function run(array $whiteList, array $blackList = array(), array $extensions = array())
    {
        $blackListStr = ' ';
        foreach ($blackList as $file) {
            $file = escapeshellarg(trim($file));
            if (!$file) {
                continue;
            }
            $blackListStr .= '--exclude ' . $file . ' ';
        }

        $command =  'phpcpd'
            . ' --log-pmd ' . escapeshellarg($this->_reportFile)
            . ' --min-lines 13'
            . $blackListStr
            . ' ' .realpath(__DIR__ . '/../../../../../../../../');

        exec($command, $output, $exitCode);

        return !(bool)$exitCode;
    }

}
