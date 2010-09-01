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
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating resource model
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Resource_Rating extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('rating/rating', 'rating_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Rating_Model_Resource_Rating
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'rating_code',
            'title' => /* Mage::helper('rating')->__('Rating with the same title')*/ ''
        ));
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $adapter = $this->_getReadAdapter();
        
        $table      = $this->getMainTable();
        $storeId    = (int)Mage::app()->getStore()->getId();
        $select     = parent::_getLoadSelect($field, $value, $object);
        $codeExpr   = $this->_getReadAdapter()->getCheckSql('title.value IS NULL', "{$table}.rating_code", 'title.value');
        $fieldIdentifier = $adapter->quoteIdentifier($field);
        
        $select->joinLeft(
                array('title' => $this->getTable('rating/rating_title')),
                "{$table}.rating_id = title.rating_id AND title.store_id = {$storeId}",
                array('rating_code' => $codeExpr))
            ->where("{$table}.{$fieldIdentifier}=?", $value);
        return $select;
    }

    /**
     * Actions after load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Rating_Model_Resource_Rating
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('rating_title'))
            ->where('rating_id=?', $object->getId());

        $data = $adapter->fetchAll($select);
        $storeCodes = array();
        foreach ($data as $row) {
            $storeCodes[$row['store_id']] = $row['value'];
        }
        if(sizeof($storeCodes)>0) {
            $object->setRatingCodes($storeCodes);
        }

        $storesSelect = $adapter->select()
            ->from($this->getTable('rating_store'))
            ->where('rating_id=?', $object->getId());

        $stores = $adapter->fetchAll($storesSelect);

        $putStores = array();
        foreach ($stores as $store) {
            $putStores[] = $store['store_id'];
        }

        $object->setStores($putStores);

        return $this;
    }

    /**
     * Actions after save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Rating_Model_Resource_Rating
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $adapter = $this->_getWriteAdapter();

        if($object->hasRatingCodes()) {
            try {
                $adapter->beginTransaction();
                $condition = $adapter->quoteInto('rating_id = ?', $object->getId());
                $adapter->delete($this->getTable('rating_title'), $condition);
                if ($ratingCodes = $object->getRatingCodes()) {
                    foreach ($ratingCodes as $storeId=>$value) {
                        if(trim($value)=='') {
                            continue;
                        }
                        $data = new Varien_Object();
                        $data->setRatingId($object->getId())
                            ->setStoreId($storeId)
                            ->setValue($value);
                        $adapter->insert($this->getTable('rating_title'), $data->getData());
                    }
                }
                $adapter->commit();
            }
            catch (Exception $e) {
                $adapter->rollBack();
            }
        }

        if($object->hasStores()) {
            try {
                $condition = $adapter->quoteInto('rating_id = ?', $object->getId());
                $adapter->delete($this->getTable('rating_store'), $condition);
                foreach ($object->getStores() as $storeId) {
                    $storeInsert = new Varien_Object();
                    $storeInsert->setStoreId($storeId);
                    $storeInsert->setRatingId($object->getId());
                    $adapter->insert($this->getTable('rating_store'), $storeInsert->getData());
                }
            }
            catch (Exception  $e) {
                $adapter->rollBack();
            }
        }

        return $this;
    }

    /**
     * Perform actions after object delete
     * Prepare rating data for reaggregate all data for reviews
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Rating_Model_Resource_Rating
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);
        $data = $this->_getEntitySummaryData($object);
        $summary = array();
        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $summary[$clone->getStoreId()][$clone->getEntityPkValue()] = $clone;
        }
        Mage::getResourceModel('review/review_summary')->reAggregate($summary);
        return $this;
    }

    /**
     * Return array of rating summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @param boolean $onlyForCurrentStore
     * @return array
     */
    public function getEntitySummary($object, $onlyForCurrentStore = true)
    {
        $data = $this->_getEntitySummaryData($object);

        if($onlyForCurrentStore) {
            foreach ($data as $row) {
                if($row['store_id']==Mage::app()->getStore()->getId()) {
                    $object->addData( $row );
                }
            }
            return $object;
        }

        $result = array();

        //$stores = Mage::app()->getStore()->getResourceCollection()->load();
        $stores = Mage::getModel('core/store')->getResourceCollection()->load();

        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $result[$clone->getStoreId()] = $clone;
        }

        $usedStoresId = array_keys($result);

        foreach ($stores as $store) {
               if (!in_array($store->getId(), $usedStoresId)) {
                    $clone = clone $object;
                    $clone->setCount(0);
                    $clone->setSum(0);
                    $clone->setStoreId($store->getId());
                    $result[$store->getId()] = $clone;
               }
        }

        return array_values($result);
    }

    /**
     * Return data of rating summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @return array
     */
    protected function _getEntitySummaryData($object)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('rating_vote' => $this->getTable('rating_vote')),
                array())
            ->join(array('review' => $this->getTable('review/review')),
                'rating_vote.review_id=review.review_id',
                array())
            ->joinLeft(array('review_store' => $this->getTable('review/review_store')),
                'rating_vote.review_id=review_store.review_id',
                array())
            ->join(array('rating_store' => $this->getTable('rating/rating_store')),
                'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                array())
            ->join(array('review_status' => $this->getTable('review/review_status')),
                'review.status_id = review_status.status_id',
                array())
            ->columns(array(
                'entity_pk_value' => 'rating_vote.entity_pk_value',
                'sum'             => "SUM(rating_vote.{$adapter->quoteIdentifier('percent')})",
                'count'           => 'COUNT(*)',
                'review_store.store_id'
            ))
            ->where('review_status.status_code = ?', 'approved')
            ->group('rating_vote.entity_pk_value')
            ->group('review_store.store_id');
        
        $entityPkValue = $object->getEntityPkValue();
        if ($entityPkValue) {
            $select->where('rating_vote.entity_pk_value=?', $entityPkValue);
        }

        return $adapter->fetchAll($select);
    }

    /**
     * Review summary
     *
     * @param Mage_Rating_Model_Rating $object
     * @param boolean $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($object, $onlyForCurrentStore = true)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('rating_vote' => $this->getTable('rating_vote')),
                array())
            ->joinLeft(array('review_store' => $this->getTable('review/review_store')),
                'rating_vote.review_id = review_store.review_id',
                array())
            ->join(array('rating_store' => $this->getTable('rating/rating_store')),
                'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                array())
            ->columns(array(
                'sum'   => "SUM(rating_vote.{$adapter->quoteIdentifier('percent')})",
                'count' => 'COUNT(*)',
                'review_store.store_id'))
            ->where('rating_vote.review_id = ?', $object->getReviewId())
            ->group('rating_vote.review_id')
            ->group('review_store.store_id');
        
        $data = $adapter->fetchAll($select);

        if($onlyForCurrentStore) {
            foreach ($data as $row) {
                if($row['store_id']==Mage::app()->getStore()->getId()) {
                    $object->addData( $row );
                }
            }
            return $object;
        }

        $result = array();

        $stores = Mage::app()->getStore()->getResourceCollection()->load();

        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData( $row );
            $result[$clone->getStoreId()] = $clone;
        }

        $usedStoresId = array_keys($result);

        foreach ($stores as $store) {
               if (!in_array($store->getId(), $usedStoresId)) {
                   $clone = clone $object;
                $clone->setCount(0);
                $clone->setSum(0);
                $clone->setStoreId($store->getId());
                $result[$store->getId()] = $clone;

               }
        }

        return array_values($result);
    }

    /**
     * Get rating entity type id by code
     *
     * @param string $entityCode
     * @return int
     */
    public function getEntityIdByCode($entityCode)
    {
        $select = $this->_getReadAdapter()->select()
            ->from( $this->getTable('rating_entity'), array('entity_id'))
            ->where('entity_code = ?', $entityCode);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Delete ratings by product id
     *
     * @param int $productId
     * @return Mage_Rating_Model_Resource_Rating
     */
    public function deleteAggregatedRatingsByProductId($productId)
    {
        $readAdapter = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        $entityId = $this->getEntityIdByCode(Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE);
        $select = $readAdapter->select()
            ->from($this->getTable('rating/rating'), array('rating_id'))
            ->where('entity_id = ?', $entityId);
        $ratings = $readAdapter->fetchCol($select);

        $inCond = $readAdapter->prepareSqlCondition("rating_id", array(
            "in" => $ratings
        ));

        if( is_array($ratings) && count($ratings) > 0 ) {
            $writeAdapter->delete($this->getTable('rating/rating_vote_aggregated'), array(
                'entity_pk_value=?' => $productId,
                $inCond
            ));
        }
        return $this;
    }
}
