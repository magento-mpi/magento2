<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource;

/**
 * Catalog search recommendations resource model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Recommendations extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Search query model
     *
     * @var \Magento\CatalogSearch\Model\Query
     */
    protected $_searchQueryModel;

    /**
     * Construct
     *
     * @param \Magento\App\Resource $resource
     * @param \Magento\CatalogSearch\Model\QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\CatalogSearch\Model\QueryFactory $queryFactory
    ) {
        parent::__construct($resource);
        $this->_searchQueryModel = $queryFactory->create();
    }

    /**
     * Init main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalogsearch_recommendations', 'id');
    }

    /**
     * Save search relations
     *
     * @param int $queryId
     * @param array $relatedQueries
     * @return $this
     */
    public function saveRelatedQueries($queryId, $relatedQueries = array())
    {
        $adapter = $this->_getWriteAdapter();
        $whereOr = array();
        if (count($relatedQueries) > 0) {
            $whereOr[] = implode(
                ' AND ',
                array(
                    $adapter->quoteInto('query_id=?', $queryId),
                    $adapter->quoteInto('relation_id NOT IN(?)', $relatedQueries)
                )
            );
            $whereOr[] = implode(
                ' AND ',
                array(
                    $adapter->quoteInto('relation_id = ?', $queryId),
                    $adapter->quoteInto('query_id NOT IN(?)', $relatedQueries)
                )
            );
        } else {
            $whereOr[] = $adapter->quoteInto('query_id = ?', $queryId);
            $whereOr[] = $adapter->quoteInto('relation_id = ?', $queryId);
        }
        $whereCond = '(' . implode(') OR (', $whereOr) . ')';
        $adapter->delete($this->getMainTable(), $whereCond);

        $existsRelatedQueries = $this->getRelatedQueries($queryId);
        $neededRelatedQueries = array_diff($relatedQueries, $existsRelatedQueries);
        foreach ($neededRelatedQueries as $relationId) {
            $adapter->insert($this->getMainTable(), array("query_id" => $queryId, "relation_id" => $relationId));
        }
        return $this;
    }

    /**
     * Retrieve related search queries
     *
     * @param int|array $queryId
     * @param bool $limit
     * @param bool $order
     * @return array
     */
    public function getRelatedQueries($queryId, $limit = false, $order = false)
    {
        $collection = $this->_searchQueryModel->getResourceCollection();
        $adapter = $this->_getReadAdapter();

        $queryIdCond = $adapter->quoteInto('main_table.query_id IN (?)', $queryId);

        $collection->getSelect()->join(
            array('sr' => $collection->getTable('catalogsearch_recommendations')),
            '(sr.query_id=main_table.query_id OR sr.relation_id=main_table.query_id) AND ' . $queryIdCond
        )->reset(
            \Zend_Db_Select::COLUMNS
        )->columns(
            array(
                'rel_id' => $adapter->getCheckSql('main_table.query_id=sr.query_id', 'sr.relation_id', 'sr.query_id')
            )
        );
        if (!empty($limit)) {
            $collection->getSelect()->limit($limit);
        }
        if (!empty($order)) {
            $collection->getSelect()->order($order);
        }

        $queryIds = $adapter->fetchCol($collection->getSelect());
        return $queryIds;
    }

    /**
     * Retrieve related search queries by single query
     *
     * @param string $query
     * @param array $params
     * @param int $searchRecommendationsCount
     * @return array
     */
    public function getRecommendationsByQuery($query, $params, $searchRecommendationsCount)
    {
        $this->_searchQueryModel->loadByQuery($query);

        if (isset($params['store_id'])) {
            $this->_searchQueryModel->setStoreId($params['store_id']);
        }
        $relatedQueriesIds = $this->loadByQuery($query, $searchRecommendationsCount);
        $relatedQueries = array();
        if (count($relatedQueriesIds)) {
            $adapter = $this->_getReadAdapter();
            $mainTable = $this->_searchQueryModel->getResourceCollection()->getMainTable();
            $select = $adapter->select()->from(
                array('main_table' => $mainTable),
                array('query_text', 'num_results')
            )->where(
                'query_id IN(?)',
                $relatedQueriesIds
            )->where(
                'num_results > 0'
            );
            $relatedQueries = $adapter->fetchAll($select);
        }

        return $relatedQueries;
    }

    /**
     * Retrieve search terms which are started with $queryWords
     *
     * @param string $query
     * @param int $searchRecommendationsCount
     * @return array
     */
    protected function loadByQuery($query, $searchRecommendationsCount)
    {
        $adapter = $this->_getReadAdapter();
        $queryId = $this->_searchQueryModel->getId();
        $relatedQueries = $this->getRelatedQueries($queryId, $searchRecommendationsCount, 'num_results DESC');
        if ($searchRecommendationsCount - count($relatedQueries) < 1) {
            return $relatedQueries;
        }

        $queryWords = array($query);
        if (strpos($query, ' ') !== false) {
            $queryWords = array_unique(array_merge($queryWords, explode(' ', $query)));
            foreach ($queryWords as $key => $word) {
                $queryWords[$key] = trim($word);
                if (strlen($word) < 3) {
                    unset($queryWords[$key]);
                }
            }
        }

        $likeCondition = array();
        foreach ($queryWords as $word) {
            $likeCondition[] = $adapter->quoteInto('query_text LIKE ?', $word . '%');
        }
        $likeCondition = implode(' OR ', $likeCondition);

        $select = $adapter->select()->from(
            $this->_searchQueryModel->getResource()->getMainTable(),
            array('query_id')
        )->where(
            new \Zend_Db_Expr($likeCondition)
        )->where(
            'store_id=?',
            $this->_searchQueryModel->getStoreId()
        )->order(
            'num_results DESC'
        )->limit(
            $searchRecommendationsCount + 1
        );
        $ids = $adapter->fetchCol($select);

        if (!is_array($ids)) {
            $ids = array();
        }

        $key = array_search($queryId, $ids);
        if ($key !== false) {
            unset($ids[$key]);
        }
        $ids = array_unique(array_merge($relatedQueries, $ids));
        $ids = array_slice($ids, 0, $searchRecommendationsCount);
        return $ids;
    }
}
