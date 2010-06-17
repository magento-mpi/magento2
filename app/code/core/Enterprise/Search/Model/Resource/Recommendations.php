<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search recommendations resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Resource_Recommendations extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init main table
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_search/recommendations', 'id');
    }

    /**
     * Save search relations
     *
     * @param int $queryId
     * @param array $relatedQueries
     * @return Enterprise_Search_Model_Resource_Query
     */
    public function saveRelatedQueries($queryId, $relatedQueries = array())
    {
        $adapter = $this->_getWriteAdapter();
        if (count($relatedQueries) > 0) {
            $inCond = implode(",", $relatedQueries);
            $whereCond = "(query_id={$queryId} AND relation_id NOT IN({$inCond})) OR (relation_id={$queryId} AND query_id NOT IN({$inCond}))";
        } else {
            $whereCond = "(query_id={$queryId}) OR (relation_id={$queryId})";
        }

        $adapter->delete($this->getMainTable(), $whereCond);

        $existsRelatedQueries = $this->getRelatedQueries($queryId);
        $neededRelatedQueries = array_diff($relatedQueries, $existsRelatedQueries);
        foreach ($neededRelatedQueries as $relationId) {
            $adapter->insert($this->getMainTable(), array(
                "query_id"    => $queryId,
                "relation_id" => $relationId
            ));
        }
        return $this;
    }

    /**
     * Retrieve related search queries
     *
     * @param int $queryId
     * @return array
     */
    public function getRelatedQueries($queryId, $limit = false, $order = false)
    {
        $queryIds = array();
        $collection = Mage::getModel('catalogsearch/query')->getResourceCollection();
        $collection->getSelect()
            ->join(array("sr" => $collection->getTable("enterprise_search/recommendations")),
                 "(sr.query_id=main_table.query_id OR sr.relation_id=main_table.query_id)
                   AND main_table.query_id={$queryId}")
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                 "rel_id" => new Zend_Db_Expr("IF(main_table.query_id=sr.query_id, sr.relation_id, sr.query_id)")
            ));
        if (!empty($limit)) {
            $collection->getSelect()->limit($limit);
        }
        $res = $collection->toArray();
        foreach ($res["items"] as $id) {
            $queryIds[] = (int)$id["rel_id"];
        }
        return $queryIds;
    }

    /**
     * Retrieve related search queries by single query
     *
     * @param string $query
     */
    public function getRecommendationsByQuery($query, $params, $searchRecommendationsCount)
    {
        $model = Mage::getModel('catalogsearch/query');
        if (isset($params['store_id'])) {
            $model->setStoreId($params['store_id']);
        }
        $model->loadByQuery($query);
        $termId = $model->getId();
        $relatedQueriesIds = $this->getRelatedQueries($termId);

        // Automatic recommendations
        if (strpos($query, " ") !== false && count($relatedQueriesIds < $searchRecommendationsCount)) {
            $queryWords = explode(" ", $query);
            $queryWords = array_unique($queryWords);
            foreach ($queryWords as $word) {
                $model->loadByQuery($word);
                $wordTermId = $model->getId();
                $wordRelatedQueriesIds = $this->getRelatedQueries($wordTermId);
                $relatedQueriesIds = array_merge($relatedQueriesIds, $wordRelatedQueriesIds);
            }
        }
        $relatedQueriesIds = array_unique($relatedQueriesIds);
        $relatedQueries = array();
        if (count($relatedQueriesIds)) {
            $collection = Mage::getModel('catalogsearch/query')
                ->getResourceCollection();
            $collection
                ->addFieldToFilter("query_id", $relatedQueriesIds)
                ->getSelect()
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array('query_text', 'num_results'))
                    ->order("main_table.num_results DESC")
                    ->limit($searchRecommendationsCount)
            ;
            $relatedQueries = $collection->toArray();
        }
        return $relatedQueries;
    }
}
