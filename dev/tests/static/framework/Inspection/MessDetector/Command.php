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
     * @param array $whiteList Files/folders to be inspected
     * @param array $blackList Files/folders to be excluded from the inspection
     */
    public function __construct($rulesetFile, $reportFile, array $whiteList, array $blackList = array())
    {
        parent::__construct($reportFile, $whiteList, $blackList);
        $this->_rulesetFile = $rulesetFile;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpmd --version';
    }

    /**
     * @return string
     */
    protected function _buildShellCmd()
    {
        $whiteList = $this->_whiteList;
        $whiteList = implode(',', $whiteList);
        $whiteList = escapeshellarg($whiteList);

        $blackList = '';
        if ($this->_blackList) {
            foreach ($this->_blackList as $fileOrDir) {
                $fileOrDir = str_replace('/', DIRECTORY_SEPARATOR, $fileOrDir);
                $blackList .= ($blackList ? ',' : '') . $fileOrDir;
            }
            $blackList = '--exclude ' . escapeshellarg($blackList);
        }

        return 'phpmd'
            . ' ' . $whiteList
            . ' xml'
            . ' ' . escapeshellarg($this->_rulesetFile)
            . ($blackList ? ' ' . $blackList : '')
            . ' --reportfile ' . escapeshellarg($this->_reportFile)
        ;
    }
}
