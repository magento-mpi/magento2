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
 * Queue for running scenarios
 */
class Magento_Scenario_Queue
{
    /**
     * Tests configuration holder
     *
     * @var Magento_Config
     */
    protected $_config;

    /**
     * Logger, used to output messages
     *
     * @param Zend_Log
     */
    protected $_logger;

    /**
     * Number of total scenarios
     *
     * @var int
     */
    protected $_numScenarios;

    /**
     * Number of failed scenarios
     *
     * @var int
     */
    protected $_numFailedScenarios;

    /**
     * Queue of scenarios in an optimal order to be run
     *
     * @var array
     */
    protected $_queue = array();

    /**
     * Application instance, used to apply fixtures to it
     *
     * @var Magento_Application
     */
    protected $_application;

    /**
     * @param Magento_Config $config
     * @param Magento_Application|null $application
     */
    public function __construct($config, $application = null)
    {
        $this->_config = $config;
        $this->_logger = $config->getLogger();
        $this->_application = $application ?: new Magento_Application($config, new Magento_Shell(false));
        $this->_initCounters();
    }

    /**
     * Reset all the counters to the default state
     *
     * @return Magento_Scenario_Queue
     */
    protected function _initCounters()
    {
        $this->_numScenarios = count($this->_config->getScenarios());
        $this->_numFailedScenarios = 0;
        return $this;
    }

    /**
     * Run all the scenarios
     */
    public function run()
    {
        $this->_composeQueue()
            ->_runQueue();
    }

    /**
     * Composes scenarios queue in an optimal order, so that installation (cleanup) is run as rarely as possible
     *
     * @return Magento_Scenario_Queue
     */
    protected function _composeQueue()
    {
        $this->_queue = array();
        $this->_queue = $this->_config->getScenarios();
        return $this;
    }

    /**
     * Runs queue of scenarios
     */
    protected function _runQueue()
    {
        $this->_initCounters();

        $scenario = new Magento_Scenario(
            new Magento_Shell,
            $this->_config->getJMeterPath(),
            $this->_config->getReportDir(),
            $this->_logger
        );

        $scenarioNum = 1;
        foreach ($this->_queue as $scenarioFile => $scenarioConfig) {
            $this->_logger->info('-----------------------------------------------');
            $this->_logger->info("Scenario $scenarioNum of {$this->_numScenarios}: '$scenarioFile'");
            try {
                $this->_application->applyFixtures($scenarioConfig['fixtures']);
                $this->_logger->info('Running scenario...');
                $scenario->run($scenarioFile, $scenarioConfig['arguments'], $scenarioConfig['settings']);
                $this->_logger->info('Scenario completed successfully');
            } catch (Exception $e) {
                $this->_logger->err($e->getMessage());
                $this->_numFailedScenarios++;
            }
            $this->_logger->info('-----------------------------------------------');
            $scenarioNum++;
        }
    }

    /**
     * Return total scenarios
     *
     * @return int
     */
    public function getNumScenarios()
    {
        return $this->_numScenarios;
    }

    /**
     * Return number of scenarios, that have failed during the last run
     *
     * @return int
     */
    public function getNumFailedScenarios()
    {
        return $this->_numFailedScenarios;
    }
}
