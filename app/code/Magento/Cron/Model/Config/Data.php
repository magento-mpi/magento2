<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Config;

/**
 * Prepare cron jobs data
 */
class Data extends \Magento\Config\Data
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
     * @param \Magento\Cron\Model\Config\Reader\Xml $reader
     * @param \Magento\Config\ScopeInterface        $configScope
     * @param \Magento\Config\CacheInterface        $cache
     * @param \Magento\Cron\Model\Config\Reader\Db  $dbReader
     * @param string                               $cacheId
     */
    public function __construct(
        \Magento\Cron\Model\Config\Reader\Xml $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        \Magento\Cron\Model\Config\Reader\Db $dbReader,
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
