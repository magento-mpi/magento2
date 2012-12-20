<?php
/**
 * Standard profiler driver that uses outputs for displaying profiling results.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard implements Magento_Profiler_DriverInterface
{
    /**
     * Storage for timers statistics
     *
     * @var Magento_Profiler_Driver_Standard_Stat
     */
    protected $_stat;

    /**
     * List of profiler driver outputs
     *
     * @var Magento_Profiler_Driver_Standard_OutputInterface[]
     */
    protected $_outputs = array();

    /**
     * Constructor
     *
     * @param Magento_Profiler_Driver_Configuration|null $configuration
     */
    public function __construct(Magento_Profiler_Driver_Configuration $configuration = null)
    {
        $this->_initOutputs($configuration);
        $this->_initStat($configuration);
        register_shutdown_function(array($this, 'display'));
    }

    /**
     * Init outputs by configuration
     *
     * @param Magento_Profiler_Driver_Configuration|null $configuration
     */
    protected function _initOutputs(Magento_Profiler_Driver_Configuration $configuration = null)
    {
        if (!$configuration) {
            return;
        }
        $outputs = array();
        if ($configuration->hasValue('outputs')) {
            $outputs = $configuration->getArrayValue('outputs');
        } elseif ($configuration->hasValue('output')) {
            $outputs[] = $configuration->getValue('output');
        }
        if ($outputs) {
            $outputFactory = $this->_getOutputFactory($configuration);
            foreach ($outputs as $code => $outputConfig) {
                if (!$outputConfig instanceof Magento_Profiler_Driver_Standard_Output_Configuration) {
                    if (is_numeric($outputConfig) && $outputConfig && !is_numeric($code)) {
                        $outputConfig = array(
                            'type' => $code
                        );
                    } elseif (!is_numeric($outputConfig) && is_string($outputConfig)) {
                        $outputConfig = array(
                            'type' => $outputConfig
                        );
                    } elseif (!is_array($outputConfig)) {
                        continue;
                    }
                    $outputConfig = new Magento_Profiler_Driver_Standard_Output_Configuration($outputConfig);
                }
                if (!$outputConfig->hasTypeValue() && !is_numeric($code)) {
                    $outputConfig->setTypeValue($code);
                }
                if (!$outputConfig->hasBaseDirValue() && $configuration->getBaseDirValue()) {
                    $outputConfig->setBaseDirValue($configuration->getBaseDirValue());
                }
                $this->registerOutput($outputFactory->create($outputConfig));
            }
        }
    }

    /**
     * Gets output factory from configuration or create new one
     *
     * @param Magento_Profiler_Driver_Configuration|null $configuration
     * @return Magento_Profiler_Driver_Standard_Output_Factory
     */
    protected function _getOutputFactory(Magento_Profiler_Driver_Configuration $configuration = null)
    {
        if ($configuration
            && $configuration->getValue('outputFactory') instanceof Magento_Profiler_Driver_Standard_Output_Factory
        ) {
            $result = $configuration->getValue('outputFactory');
        } else {
            $result = new Magento_Profiler_Driver_Standard_Output_Factory();
        }
        return $result;
    }

    /**
     * Init timers statistics object from configuration or create new one
     *
     * @param Magento_Profiler_Driver_Configuration $configuration|null
     */
    protected function _initStat(Magento_Profiler_Driver_Configuration $configuration = null)
    {
        if ($configuration && $configuration->getValue('stat') instanceof Magento_Profiler_Driver_Standard_Stat) {
            $this->_stat = $configuration->getValue('stat');
        } else {
            $this->_stat = new Magento_Profiler_Driver_Standard_Stat();
        }
    }

    /**
     * Clear collected statistics for specified timer or for whole profiler if timer id is omitted
     *
     * @param string|null $timerId
     */
    public function clear($timerId = null)
    {
        $this->_stat->clear($timerId);
    }

    /**
     * Start collecting statistics for specified timer
     *
     * @param string $timerId
     * @param array|null $tags
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function start($timerId, array $tags = null)
    {
        $this->_stat->start($timerId, microtime(true), memory_get_usage(true), memory_get_usage());
    }

    /**
     * Stop recording statistics for specified timer.
     *
     * @param string $timerId
     */
    public function stop($timerId)
    {
        $this->_stat->stop($timerId, microtime(true), memory_get_usage(true), memory_get_usage());
    }

    /**
     * Register profiler output instance to display profiling result at the end of execution
     *
     * @param Magento_Profiler_Driver_Standard_OutputInterface $output
     */
    public function registerOutput(Magento_Profiler_Driver_Standard_OutputInterface $output)
    {
        $this->_outputs[] = $output;
    }

    /**
     * Display collected statistics with registered outputs
     */
    public function display()
    {
        if (Magento_Profiler::isEnabled()) {
            foreach ($this->_outputs as $output) {
                $output->display($this->_stat);
            }
        }
    }
}
