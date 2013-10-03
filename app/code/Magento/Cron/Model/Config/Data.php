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
namespace Magento\Cron\Model\Config;

class Data extends \Magento\Config\Data
{
    /**
     * Initialize parameters
     *
     * @param \Magento\Cron\Model\Config\Reader\Xml $reader
     * @param \Magento\Config\CacheInterface        $cache
     * @param \Magento\Cron\Model\Config\Reader\Db  $dbReader
     * @param string                               $cacheId
     */
    public function __construct(
        \Magento\Cron\Model\Config\Reader\Xml $reader,
        \Magento\Config\CacheInterface $cache,
        \Magento\Cron\Model\Config\Reader\Db $dbReader,
        $cacheId = 'crontab_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
        $this->merge($dbReader->get());
    }

    /**
     * Merge cron jobs and return
     *
     * @return array
     */
    public function getJobs()
    {
        return $this->get();
    }
}
