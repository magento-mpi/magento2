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
namespace Magento\CatalogSearch\Model\Resource\Query;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Store for filter
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Init model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogSearch\Model\Query', 'Magento\CatalogSearch\Model\Resource\Query');
    }

    /**
     * Set Store ID for filter
     *
     * @param mixed $store
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
     */
    public function setStoreId($store)
    {
        if ($store instanceof \Magento\Core\Model\Store) {
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
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
     */
    public function setQueryFilter($query)
    {
        $ifSynonymFor = $this->getConnection()
            ->getIfNullSql('synonym_for', 'query_text');
        $this->getSelect()->reset(\Zend_Db_Select::FROM)->distinct(true)
            ->from(
                array('main_table' => $this->getTable('catalogsearch_query')),
                array('query'      => $ifSynonymFor, 'num_results')
            )
            ->where('num_results > 0 AND display_in_terms = 1 AND query_text LIKE ?',
                \Mage::getResourceHelper('Magento_Core')->addLikeEscape($query, array('position' => 'start')))
            ->order('popularity ' . \Magento\DB\Select::SQL_DESC);
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
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
     */
    public function setPopularQueryFilter($storeIds = null)
    {
        $ifSynonymFor = new \Zend_Db_Expr($this->getConnection()
            ->getCheckSql("synonym_for IS NOT NULL AND synonym_for != ''", 'synonym_for', 'query_text'));

        $this->getSelect()
            ->reset(\Zend_Db_Select::FROM)
            ->reset(\Zend_Db_Select::COLUMNS)
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
            $this->addStoreFilter(\Mage::app()->getStore()->getId());
            $this->getSelect()->where('num_results > 0');
        }

        $this->getSelect()->order(array('popularity desc','name'));

        return $this;
    }

    /**
     * Set Recent Queries Order
     *
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
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
     * @return \Magento\CatalogSearch\Model\Resource\Query\Collection
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
