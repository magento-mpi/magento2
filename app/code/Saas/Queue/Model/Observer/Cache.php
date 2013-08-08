<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Queue
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Executing jobs to refresh cache
 */
class Saas_Queue_Model_Observer_Cache extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * @var Magento_Core_Model_CacheInterface $_cache
     */
    protected $_cache;

    /**
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function __construct(Magento_Core_Model_CacheInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Show that customer should not be notified about the task execution by email
     *
     * @return bool
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Task to refresh cache with defined type
     *
     * @param  Magento_Event_Observer $observer
     * @return Saas_Queue_Model_Worker_Cache
     */
    public function processRefreshCache(Magento_Event_Observer $observer)
    {
        $cacheTypes = $observer->getEvent()->getCacheTypes();
        if ($cacheTypes) {
            foreach ($cacheTypes as $type) {
                $this->_cache->cleanType($type);
            }
        } else {
            $this->processRefreshAllCache();
        }
        return $this;
    }

    /**
     * Refresh all cache
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return Saas_Queue_Model_Worker_Cache
     */
    public function processRefreshAllCache()
    {
        foreach ($this->_cache->getTypes() as $type => $typeDescription) {
            $this->_cache->cleanType($type);
        }
        return $this;
    }
}
