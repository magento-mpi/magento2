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
 * PHP Code Mess v1.3.3 tool wrapper
 */
class Magento_TestFramework_CodingStandard_Tool_CodeMessDetector
    implements Magento_TestFramework_CodingStandard_ToolInterface
{
    /**
     * Ruleset directory
     *
     * @var string
     */
    protected $_rulesetFile;

    /**
     * Report file
     *
     * @var string
     */
    protected $_reportFile;

    /**
     * Constructor
     *
     * @param string $rulesetDir Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetFile, $reportFile)
    {
        $this->_reportFile = $reportFile;
        $this->_rulesetFile = $rulesetFile;
    }

    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return class_exists('PHP_PMD_TextUI_Command');
    }

    /**
     * Run tool for files specified
     *
     * @param array $whiteList Files/directories to be inspected
     * @param array $blackList Files/directories to be excluded from the inspection
     * @param array $extensions Array of alphanumeric strings, for example: 'php', 'xml', 'phtml', 'css'...
     *
     * @return int
     */
    public function run(array $whiteList, array $blackList = array(), array $extensions = array())
    {
        $commandLineArguments = array('run_file_mock', //emulate script name in console arguments
            implode(',', $whiteList),
            'xml', //report format
            $this->_rulesetFile,
            '--exclude' , str_replace('/', DIRECTORY_SEPARATOR, implode(',', $blackList)),
            '--reportfile' , $this->_reportFile
        );

        $options = new PHP_PMD_TextUI_CommandLineOptions($commandLineArguments);

        $command = new PHP_PMD_TextUI_Command();

        return $command->run($options);
    }

}
