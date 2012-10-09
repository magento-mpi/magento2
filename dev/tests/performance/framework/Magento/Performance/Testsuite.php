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
     * @var callable
     */
    protected $_onScenarioRun;

    /**
     * @var callable
     */
    protected $_onScenarioFailure;

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
     */
    public function run()
    {
        $scenarios = $this->_getOptimizedScenarioList();

        foreach ($scenarios as $scenarioFile) {
            $scenarioArguments = $this->_config->getScenarioArguments($scenarioFile);
            $scenarioSettings = $this->_config->getScenarioSettings($scenarioFile);
            $scenarioFixtures = $this->_config->getScenarioFixtures($scenarioFile);

            $this->_application->applyFixtures($scenarioFixtures);

            $this->_notifyScenarioRun($scenarioFile);

            /* warm up cache, if any */
            if (empty($scenarioSettings[self::SETTING_SKIP_WARM_UP])) {
                $warmUpArgs = new Magento_Performance_Scenario_Arguments(
                    $this->_warmUpArguments + (array)$scenarioArguments
                );
                try {
                    $this->_scenarioHandler->run($scenarioFile, $warmUpArgs);
                } catch (Magento_Performance_Scenario_FailureException $scenarioFailure) {
                    // do not notify about failed warm up
                }
            }

            /* full run with reports recording */
            $scenarioName = pathinfo($scenarioFile, PATHINFO_FILENAME);
            $reportFile = $this->_config->getReportDir() . DIRECTORY_SEPARATOR . $scenarioName . '.jtl';
            try {
                $this->_scenarioHandler->run($scenarioFile, $scenarioArguments, $reportFile);
            } catch (Magento_Performance_Scenario_FailureException $scenarioFailure) {
                $this->_notifyScenarioFailure($scenarioFailure);
            }
        }
    }

    /**
     * Set callback for scenario run event
     *
     * @param callable $callback
     */
    public function onScenarioRun($callback)
    {
        $this->_validateCallback($callback);
        $this->_onScenarioRun = $callback;
    }

    /**
     * Set callback for scenario failure event
     *
     * @param callable $callback
     */
    public function onScenarioFailure($callback)
    {
        $this->_validateCallback($callback);
        $this->_onScenarioFailure = $callback;
    }

    /**
     * Validate whether a callback refers to a valid function/method that can be invoked
     *
     * @param callable $callback
     * @throws BadFunctionCallException
     */
    protected function _validateCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new BadFunctionCallException('Callback is invalid.');
        }
    }

    /**
     * Notify about scenario run event
     *
     * @param string $scenarioFile
     */
    protected function _notifyScenarioRun($scenarioFile)
    {
        if ($this->_onScenarioRun) {
            call_user_func($this->_onScenarioRun, $scenarioFile);
        }
    }

    /**
     * Notify about scenario failure event
     *
     * @param Magento_Performance_Scenario_FailureException $scenarioFailure
     */
    protected function _notifyScenarioFailure(Magento_Performance_Scenario_FailureException $scenarioFailure)
    {
        if ($this->_onScenarioFailure) {
            call_user_func($this->_onScenarioFailure, $scenarioFailure);
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
