<?php
/**
 * Profiler configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Configuration
{
    /**
     * Base directory of application
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Driver factory
     *
     * @var Magento_Profiler_Driver_Factory
     */
    protected $_driverFactory;

    /**
     * List of drivers configuration
     *
     * @var Magento_Profiler_Driver_Configuration[]
     */
    protected $_driverConfigs = array();

    /**
     * List of filters by tag
     *
     * @var array
     */
    protected $_tagFilters = array();

    /**
     * Constructor
     *
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->_baseDir = $baseDir;
    }

    /**
     * Get value of base directory
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_baseDir;
    }

    /**
     * Init list of driver configuration
     *
     * @param array $driversData
     */
    public function initDriverConfigurations(array $driversData)
    {
        $this->_driverConfigs = array();
        foreach ($driversData as $code => $driverData) {
            if (is_scalar($driverData)) {
                if (!$driverData) {
                    continue;
                } else {
                    $driverData = array();
                }
            }
            $driverConfiguration = new Magento_Profiler_Driver_Configuration($driverData);
            if (!$driverConfiguration->hasTypeValue() && !is_numeric($code)) {
                $driverConfiguration->setTypeValue($code);
            }
            if (!$driverConfiguration->hasBaseDirValue()) {
                $driverConfiguration->setBaseDirValue($this->getBaseDir());
            }
            $this->_driverConfigs[] = $driverConfiguration;
        }
    }

    /**
     * Get list of drivers configuration
     *
     * @return Magento_Profiler_Driver_Configuration[]
     */
    public function getDriverConfigs()
    {
        return $this->_driverConfigs;
    }

    /**
     * Set array of tag filters
     *
     * @param array $tagFilters
     */
    public function setTagFilters(array $tagFilters)
    {
        $this->_tagFilters = $tagFilters;
    }

    /**
     * Get array of tag filters
     *
     * @return array
     */
    public function getTagFilters()
    {
        return $this->_tagFilters;
    }

    /**
     * Get driver factory
     *
     * @return Magento_Profiler_Driver_Factory
     */
    public function getDriverFactory()
    {
        if (!$this->_driverFactory) {
            $this->_driverFactory = new Magento_Profiler_Driver_Factory();
        }
        return $this->_driverFactory;
    }

    /**
     * Set driver factory
     *
     * @param Magento_Profiler_Driver_Factory $driverFactory
     */
    public function setDriverFactory(Magento_Profiler_Driver_Factory $driverFactory)
    {
        $this->_driverFactory = $driverFactory;
    }
}
