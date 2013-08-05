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
     * @var Mage_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Mage_Core_Model_Cache_TypeListInterface $cacheTypeList
     */
    public function __construct(Mage_Core_Model_Cache_TypeListInterface $cacheTypeList)
    {
        $this->_cacheTypeList = $cacheTypeList;
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
     * @return Saas_Queue_Model_Observer_Cache
     */
    public function processRefreshCache(Magento_Event_Observer $observer)
    {
        $cacheTypes = $observer->getEvent()->getCacheTypes();
        if ($cacheTypes) {
            foreach ($cacheTypes as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
        } else {
            $this->processRefreshAllCache();
        }
        return $this;
    }

    /**
     * Refresh all cache
     *
     * @return Saas_Queue_Model_Observer_Cache
     */
    public function processRefreshAllCache()
    {
        $types = array_keys($this->_cacheTypeList->getTypes());
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        return $this;
    }
}
