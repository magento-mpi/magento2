<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PHP Code Mess v1.3.3 tool wrapper
 */
namespace Magento\TestFramework\CodingStandard\Tool;

class CodeMessDetector implements \Magento\TestFramework\CodingStandard\ToolInterface
{
    /**
     * Ruleset directory
     *
     * @var string
     */
    private $rulesetFile;

    /**
     * Report file
     *
     * @var string
     */
    private $reportFile;

    /**
     * Constructor
     *
     * @param string $rulesetFile \Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetFile, $reportFile)
    {
        $this->reportFile = $reportFile;
        $this->rulesetFile = $rulesetFile;
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
     * {@inheritdoc}
     */
    public function run(array $whiteList)
    {
        if (empty($whiteList)) {
            return \PHP_PMD_TextUI_Command::EXIT_SUCCESS;
        }
        $commandLineArguments = [
            'run_file_mock', //emulate script name in console arguments
            implode(',', $whiteList),
            'xml', //report format
            $this->rulesetFile,
            '--reportfile',
            $this->reportFile,
        ];

        $options = new \PHP_PMD_TextUI_CommandLineOptions($commandLineArguments);

        $command = new \PHP_PMD_TextUI_Command();

        return $command->run($options, new \PHP_PMD_RuleSetFactory());
    }
}
