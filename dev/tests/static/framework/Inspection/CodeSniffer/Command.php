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
 * PHP Code Sniffer shell command
 */
class Inspection_CodeSniffer_Command extends Inspection_CommandAbstract
{
    /**
     * @var string
     */
    protected $_rulesetDir;

    /**
     * @var array
     */
    protected $_extensions = array();

    /**
     * Constructor
     *
     * @param string $rulesetDir Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetDir, $reportFile)
    {
        parent::__construct($reportFile);
        $this->_rulesetDir = $rulesetDir;
    }

    /**
     * Limit scanning folders by file extensions
     *
     * Array of alphanumeric strings, for example: 'php', 'xml', 'phtml', 'css'...
     *
     * @param array $extensions
     * @return Inspection_CodeSniffer_Command
     */
    public function setExtensions(array $extensions)
    {
        $this->_extensions = $extensions;
        return $this;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpcs --version';
    }

    /**
     * @param array $whiteList
     * @param array $blackList
     * @return string
     */
    protected function _buildShellCmd($whiteList, $blackList)
    {
        $whiteList = array_map('escapeshellarg', $whiteList);
        $whiteList = implode(' ', $whiteList);

        /* Note: phpcs allows regular expressions for the ignore list */
        $blackListStr = '';
        if ($blackList) {
            foreach ($blackList as $fileOrDir) {
                $fileOrDir = str_replace('/', DIRECTORY_SEPARATOR, $fileOrDir);
                $blackListStr .= ($blackListStr ? ',' : '') . preg_quote($fileOrDir);
            }
            $blackListStr = '--ignore=' . escapeshellarg($blackListStr);
        }

//        echo 'phpcs'
//            . ($blackListStr ? ' ' . $blackListStr : '')
//            . ' --standard=' . escapeshellarg($this->_rulesetDir)
//            . ' --report=checkstyle'
//            . ($this->_extensions ? ' --extensions=' . implode(',', $this->_extensions) : '')
//            . ' --report-file=' . escapeshellarg($this->_reportFile)
//            . ' -n'
//            . ' ' . $whiteList; exit();
        return 'phpcs'
            . ($blackListStr ? ' ' . $blackListStr : '')
            . ' --standard=' . escapeshellarg($this->_rulesetDir)
            . ' --report=checkstyle'
            . ($this->_extensions ? ' --extensions=' . implode(',', $this->_extensions) : '')
            . ' --report-file=' . escapeshellarg($this->_reportFile)
            . ' -n'
            . ' ' . $whiteList
        ;
    }
}
