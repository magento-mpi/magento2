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
 * PHP Copy/Paste Detector shell command
 */
class Inspection_CopyPasteDetector_Command extends Inspection_CommandAbstract
{
    /**
     * @var int|null
     */
    protected $_minLines;

    /**
     * @var int|null
     */
    protected $_minTokens;

    /**
     * Constructor
     *
     * @param string $reportFile Destination file to write inspection report to
     * @param array $whiteList Files/folders to be inspected
     * @param array $blackList Files/folders to be excluded from the inspection
     * @param int|null $minLines Minimum number of identical lines
     * @param int|null $minTokens Minimum number of identical tokens
     */
    public function __construct(
        $reportFile, array $whiteList, array $blackList = array(), $minLines = null, $minTokens = null
    ) {
        parent::__construct($reportFile, $whiteList, $blackList);
        $this->_minLines = $minLines;
        $this->_minTokens = $minTokens;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpcpd --version';
    }

    /**
     * @return string
     */
    protected function _buildShellCmd()
    {
        $whiteList = $this->_whiteList;
        $whiteList = array_map('escapeshellarg', $whiteList);
        $whiteList = implode(' ', $whiteList);

        $blackList = $this->_blackList;
        if ($blackList) {
            $blackList = array_map('escapeshellarg', $blackList);
            $blackList = '--exclude ' . implode(' --exclude ', $blackList);
        } else {
            $blackList = '';
        }

        return 'phpcpd'
            . ' --log-pmd ' . escapeshellarg($this->_reportFile)
            . ($blackList ? ' ' . $blackList : '')
            . ($this->_minLines ? ' --min-lines ' . $this->_minLines : '')
            . ($this->_minTokens ? ' --min-tokens ' . $this->_minTokens : '')
            . ' ' . $whiteList
        ;
    }

    /**
     * Runs command and produces report in html format
     *
     * @return bool
     */
    public function run()
    {
        $result = parent::run();
        if ($result) {
            $generateHtmlResult = $this->_generateHtmlReport();
            if ($generateHtmlResult === false) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Under Unix platform creates one more report in html-format.
     * The report is generated based on already existing report in xml format and XSLT conversion scheme
     * in "html_report.xslt". Returns null, if conversion tool was not found, otherwise returns true/false as a result
     * of conversion tool execution.
     *
     * @return bool|null
     */
    protected function _generateHtmlReport()
    {
        $isWindows = strncasecmp(PHP_OS, 'win', 3) == 0;
        if ($isWindows) {
            return null;
        }
        if (!$this->_execShellCmd('xsltproc --version')) {
            return null;
        }
        $xsltFile = __DIR__ . '/html_report.xslt';
        $result = $this->_execShellCmd("xsltproc {$xsltFile} {$this->_reportFile} > {$this->_reportFile}.html");
        return $result;
    }
}
