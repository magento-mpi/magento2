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
 * PHP Mess Detector shell command
 */
class Inspection_MessDetector_Command extends Inspection_CommandAbstract
{
    /**
     * @var string
     */
    protected $_rulesetFile;

    /**
     * Constructor
     *
     * @param string $rulesetFile File that declares the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetFile, $reportFile)
    {
        parent::__construct($reportFile);
        $this->_rulesetFile = $rulesetFile;
    }

    /**
     * Get path to the ruleset file
     *
     * @return string
     */
    public function getRulesetFile()
    {
        return $this->_rulesetFile;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpmd --version';
    }

    /**
     * @param array $whiteList
     * @param array $blackList
     * @return string
     */
    protected function _buildShellCmd($whiteList, $blackList)
    {
        $whiteList = implode(',', $whiteList);
        $whiteList = escapeshellarg($whiteList);

        $blackListStr = '';
        if ($blackList) {
            foreach ($blackList as $fileOrDir) {
                $fileOrDir = str_replace('/', DIRECTORY_SEPARATOR, $fileOrDir);
                $blackListStr .= ($blackListStr ? ',' : '') . $fileOrDir;
            }
            $blackListStr = '--exclude ' . escapeshellarg($blackListStr);
        }

        return 'phpmd'
            . ' ' . $whiteList
            . ' xml'
            . ' ' . escapeshellarg($this->_rulesetFile)
            . ($blackListStr ? ' ' . $blackListStr : '')
            . ' --reportfile ' . escapeshellarg($this->_reportFile)
        ;
    }
}
