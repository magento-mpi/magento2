<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search query collection
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Query_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store for filter
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog search resource helper
     *
     * @var Magento_CatalogSearch_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogSearch_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogSearch_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Init model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogSearch_Model_Query', 'Magento_CatalogSearch_Model_Resource_Query');
    }

    /**
     * Set Store ID for filter
     *
     * @param mixed $store
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function setStoreId($store)
    {
        if ($store instanceof Magento_Core_Model_Store) {
            $store = $store->getId();
        }
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Retrieve Store ID Filter
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Set search query text to filter
     *
     * @param string $query
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function setQueryFilter($query)
    {
        $ifSynonymFor = $this->getConnection()
            ->getIfNullSql('synonym_for', 'query_text');
        $this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
            ->from(
                array('main_table' => $this->getTable('catalogsearch_query')),
                array('query'      => $ifSynonymFor, 'num_results')
            )
            ->where('num_results > 0 AND display_in_terms = 1 AND query_text LIKE ?',
                $this->_resourceHelper->addLikeEscape($query, array('position' => 'start')))
            ->order('popularity ' . Magento_DB_Select::SQL_DESC);
        if ($this->getStoreId()) {
            $this->getSelect()
                ->where('store_id = ?', (int)$this->getStoreId());
        }
        return $this;
    }

    /**
     * Set Popular Search Query Filter
     *
     * @param int|array $storeIds
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function setPopularQueryFilter($storeIds = null)
    {
        $ifSynonymFor = new Zend_Db_Expr($this->getConnection()
            ->getCheckSql("synonym_for IS NOT NULL AND synonym_for != ''", 'synonym_for', 'query_text'));

        $this->getSelect()
            ->reset(Zend_Db_Select::FROM)
            ->reset(Zend_Db_Select::COLUMNS)
            ->distinct(true)
            ->from(
                array('main_table' => $this->getTable('catalogsearch_query')),
                array('name' => $ifSynonymFor, 'num_results', 'popularity')
            );
        if ($storeIds) {
            $this->addStoreFilter($storeIds);
            $this->getSelect()->where('num_results > 0');
        }
        elseif (null === $storeIds) {
            $this->addStoreFilter($this->_storeManager->getStore()->getId());
            $this->getSelect()->where('num_results > 0');
        }

        $this->getSelect()->order(array('popularity desc','name'));

        return $this;
    }

    /**
     * Set Recent Queries Order
     *
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function setRecentQueryFilter()
    {
        $this->setOrder('updated_at', 'desc');
        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_CatalogSearch_Model_Resource_Query_Collection
     */
    public function addStoreFilter($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
