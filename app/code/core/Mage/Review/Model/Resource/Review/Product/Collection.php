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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Review Product Collection
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Entities alias
     *
     * @var unknown
     */
    protected $_entitiesAlias        = array();

    /**
     * Review store table
     *
     * @var unknown
     */
    protected $_reviewStoreTable;

    /**
     * Add store data flag
     *
     * @var unknown
     */
    protected $_addStoreDataFlag     = false;

    /**
     * Define module
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
        $this->setRowIdFieldName('review_id');
        $this->_reviewStoreTable = Mage::getSingleton('core/resource')->getTableName('review/review_store');
        $this->_initTables();
    }

    /**
     * init select
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Add store filter
     *
     * @param mixed $storeId
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addStoreFilter($storeId = null)
    {
        parent::addStoreFilter($storeId);
        $this->getSelect()
            ->join(array('store'=>$this->_reviewStoreTable),
                $this->getConnection()->quoteInto('rt.review_id=store.review_id AND store.store_id = ?', $storeId),
                array());
        return $this;
    }

    /**
     * Set store filter
     *
     * @param mixed $storeId
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function setStoreFilter($storeId)
    {
        $adapter = $this->Connection();
        if( is_array($storeId) && isset($storeId['eq']) ) {
            $storeId = array_shift($storeId);
        }

        if( is_array($storeId) ) {
            $inCond = $adapter->prepareSqlCondition('store.store_id', array('in' => $storeId));
            $this->getSelect()
                ->join(array('store'=>$this->_reviewStoreTable),
                    'rt.review_id=store.review_id AND ' .$inCond,
                    array())
                ->distinct(true);
        } else {
            $this->getSelect()
                ->join(array('store'=>$this->_reviewStoreTable),
                    $adapter->quoteInto('rt.review_id=store.review_id AND store.store_id = ?', $storeId),
                    array());
        }

        return $this;
    }

    /**
     * Add stores data
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('rdt.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()
            ->where('rt.entity_pk_value = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param mixed $status
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('rt.status_id = ?', $status);
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }

    /**
     * Add review summary
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addReviewSummary()
    {
        foreach( $this->getItems() as $item ) {
            $model = Mage::getModel('rating/rating');
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    /**
     * Add rote votes
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($item->getEntityId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load();
            $item->setRatingVotes( $votesCollection );
        }
        return $this;
    }

    /**
     * join fields to entity
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _joinFields()
    {
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->join(array('rt' => $reviewTable),
                'rt.entity_pk_value = e.entity_id',
                array('rt.review_id', 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id'))
            ->join(array('rdt' => $reviewDetailTable), 'rdt.review_id = rt.review_id', array());
        return $this;
    }

    /**
     * Retrive all ids for collection
     *
     * @param unknown_type $limit
     * @param unknown_type $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('rt.review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();

        $select->reset(Zend_Db_Select::HAVING);

        return $select;
    }

    /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            case 'stores':
                // No way to sort
                break;
            case 'type':
                $this->getSelect()->order('rdt.customer_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                return $this;
                break;
           case 'stores':
                $this->setStoreFilter($condition);
                return $this;
                break;
            case 'type':
                if($condition == 1) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', array('eq' => 0));
                } elseif ($condition == 2) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', array('gt' => 0));
                } else {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', array('null' => null));
                }
                $this->getSelect()->where($conditionSql);
                return $this;
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
        return $this;
    }

    /**
     * Retrieves column values
     *
     * @param string $colName
     * @return array
     */
    public function getColumnValues($colName)
    {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    /**
     * Action after load
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }

    /**
     * Add store data
     *
     */
    protected function _addStoreData()
    {
        $adapter = $this->getConnection();
        //$this->_getConditionSql('rdt.customer_id', array('null' => null));
        $reviewsIds = $this->getColumnValues('review_id');
        $storesToReviews = array();
        if (count($reviewsIds)>0) {
            $reviewIdCondition = $this->_getConditionSql('review_id', array('in' => $reviewsIds));
            $storeIdCondition = $this->_getConditionSql('store_id', array('gt' => 0));
            $select = $adapter->select()
                ->from($this->_reviewStoreTable)
                ->where($reviewIdCondition)
                ->where($storeIdCondition);
            $result = $adapter->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = array();
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if(isset($storesToReviews[$item->getReviewId()])) {
                $item->setData('stores',$storesToReviews[$item->getReviewId()]);
            } else {
                $item->setData('stores', array());
            }

        }
    }
}
