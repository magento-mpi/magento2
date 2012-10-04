<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Performance test suite represents set of performance testing scenarios
 */
class Magento_Performance_Testsuite
{
    /**
     * Do not perform scenario warm up
     */
    const SETTING_SKIP_WARM_UP = 'skip_warm_up';

    /**
     * @var Magento_Performance_Config
     */
    protected $_config;

    /**
     * Application instance to apply fixtures to
     *
     * @var Magento_Application
     */
    protected $_application;

    /**
     * @var Magento_Performance_Scenario_HandlerInterface
     */
    protected $_scenarioHandler;

    /**
     * @var array
     */
    protected $_warmUpArguments = array(
        Magento_Performance_Scenario_Arguments::ARG_USERS => 1,
        Magento_Performance_Scenario_Arguments::ARG_LOOPS => 2,
    );

    /**
     * Constructor
     *
     * @param Magento_Performance_Config $config
     * @param Magento_Application $application
     * @param Magento_Performance_Scenario_HandlerInterface $scenarioHandler
     */
    public function __construct(Magento_Performance_Config $config,
        Magento_Application $application, Magento_Performance_Scenario_HandlerInterface $scenarioHandler
    ) {
        $this->_config = $config;
        $this->_application = $application;
        $this->_scenarioHandler = $scenarioHandler;
    }

    /**
     * Run entire test suite of scenarios
     *
     * @throws Magento_Exception
     */
    public function run()
    {
        $scenarios = $this->_getOptimizedScenarioList();

        foreach ($scenarios as $scenarioFile) {
            $scenarioArguments = $this->_config->getScenarioArguments($scenarioFile);
            $scenarioSettings = $this->_config->getScenarioSettings($scenarioFile);
            $scenarioFixtures = $this->_config->getScenarioFixtures($scenarioFile);

            $this->_application->applyFixtures($scenarioFixtures);

            /* warm up cache, if any */
            if (empty($scenarioSettings[self::SETTING_SKIP_WARM_UP])) {
                $warmUpArgs = new Magento_Performance_Scenario_Arguments(
                    $this->_warmUpArguments + (array)$scenarioArguments
                );
                $this->_scenarioHandler->run($scenarioFile, $warmUpArgs);
            }

            /* full run with reports recording */
            $scenarioName = preg_replace('/\..+?$/', '', basename($scenarioFile));
            $reportFile = $this->_config->getReportDir() . DIRECTORY_SEPARATOR . $scenarioName . '.jtl';
            if (!$this->_scenarioHandler->run($scenarioFile, $scenarioArguments, $reportFile)) {
                throw new Magento_Exception("Unable to run scenario '$scenarioFile', format is not supported.");
            }
        }
    }

    /**
     * Compose optimal list of scenarios, so that Magento reinstalls will be reduced among scenario executions
     *
     * @return array
     */
    protected function _getOptimizedScenarioList()
    {
        $optimizer = new Magento_Performance_Testsuite_Optimizer();
        $scenarios = array();
        foreach ($this->_config->getScenarios() as $scenarioFile) {
            $scenarios[$scenarioFile] = $this->_config->getScenarioFixtures($scenarioFile);
        }
        return $optimizer->run($scenarios);
    }
}
