<?php
/**
 * Saas queue catalog observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Saas queue catalog observer
 *
 * @category    Saas
 * @package     Saas_Queue
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Queue_Model_Observer_Catalog extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * Instance of indexer observer model
     *
     * @var Saas_Queue_Model_Observer_Indexer
     */
    protected $_indexerObserver;

    /**
     * Indexer of cache observer model
     *
     * @var Saas_Queue_Model_Observer_Cache
     */
    protected $_cacheObserver;

    /**
     * Indexer of cache observer model
     *
     * @var Saas_Queue_Model_Observer_Config
     */
    protected $_configObserver;

    /**
     * Basic class initialization
     *
     * @param Saas_Queue_Model_Observer_Indexer $indexerObserver
     * @param Saas_Queue_Model_Observer_Cache $cacheObserver
     * @param Saas_Queue_Model_Observer_Config $configObserver
     */
    public function __construct(
        Saas_Queue_Model_Observer_Indexer $indexerObserver,
        Saas_Queue_Model_Observer_Cache $cacheObserver,
        Saas_Queue_Model_Observer_Config $configObserver
    ) {
        $this->_indexerObserver = $indexerObserver;
        $this->_cacheObserver   = $cacheObserver;
        $this->_configObserver  = $configObserver;
    }

    /**
     * {@inheritdoc}
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Reindex all processes
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Indexer
     */
    public function processRefreshCatalog(Varien_Event_Observer $observer)
    {
        $this->_configObserver->processReinitConfig($observer);
        $this->_indexerObserver->processReindexAll($observer);
        $this->_cacheObserver->processRefreshAllCache($observer);

        return $this;
    }
}
