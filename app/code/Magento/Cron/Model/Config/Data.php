<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Prepare cron jobs data
 */
class Magento_Cron_Model_Config_Data extends Magento_Config_Data
{
    /**
     * Scope visibility
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * Initialize parameters
     *
     * @param Magento_Cron_Model_Config_Reader_Xml $reader
     * @param Magento_Config_ScopeInterface        $configScope
     * @param Magento_Config_CacheInterface        $cache
     * @param Magento_Cron_Model_Config_Reader_Db  $dbReader
     * @param string                               $cacheId
     */
    public function __construct(
        Magento_Cron_Model_Config_Reader_Xml $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        Magento_Cron_Model_Config_Reader_Db $dbReader,
        $cacheId = 'crontab_config_cache'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        $this->_dbReader = $dbReader;
    }

    /**
     * Merge cron jobs and return
     *
     * @return mixed
     */
    public function getJobs()
    {
        $this->get();
        $this->merge($this->_dbReader->get());
        return $this->_data;
    }
}
