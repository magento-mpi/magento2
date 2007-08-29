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
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review collection resource model
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Review_Model_Mysql4_Review_Collection extends Varien_Data_Collection_Db
{
    protected $_reviewTable;
    protected $_reviewDetailTable;
    protected $_reviewStatusTable;
    protected $_reviewEntityTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('review_read'));

        $this->_reviewTable         = $resources->getTableName('review/review');
        $this->_reviewDetailTable   = $resources->getTableName('review/review_detail');
        $this->_reviewStatusTable   = $resources->getTableName('review/review_status');
        $this->_reviewEntityTable   = $resources->getTableName('review/review_entity');

        $this->_sqlSelect->from($this->_reviewTable)
            ->join($this->_reviewDetailTable, $this->_reviewTable.'.review_id='.$this->_reviewDetailTable.'.review_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('review/review'));
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFilter('customer',
            $this->getConnection()->quoteInto($this->_reviewDetailTable.'.customer_id=?', $customerId),
            'string');
        return $this;
    }

    /**
     * Add store filter
     *
     * @param   int $storeId
     * @return  Varien_Data_Collection_Db
     */
    public function addStoreFilter($storeId)
    {
        $this->addFilter('store',
            $this->getConnection()->quoteInto($this->_reviewDetailTable.'.store_id=?', $storeId),
            'string');

        return $this;
    }

    /**
     * Add entity filter
     *
     * @param   int|string $entity
     * @param   int $pkValue
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity, $pkValue)
    {
        Mage::log('Add entity filter to review collection');
        if (is_numeric($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_reviewTable.'.entity_id=?', $entity),
                'string');
        }
        elseif (is_string($entity)) {
            $this->_sqlSelect->join($this->_reviewEntityTable,
                $this->_reviewTable.'.entity_id='.$this->_reviewEntityTable.'.entity_id');

            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_reviewEntityTable.'.entity_code=?', $entity),
                'string');
        }

        $this->addFilter('entity_pk_value',
            $this->getConnection()->quoteInto($this->_reviewTable.'.entity_pk_value=?', $pkValue),
            'string');

        return $this;
    }

    public function addEntityInfo($entityName)
    {

        return $this;
    }

    /**
     * Add status filter
     *
     * @param   int|string $status
     * @return  Varien_Data_Collection_Db
     */
    public function addStatusFilter($status)
    {
        Mage::log('Add status filter to review collection');
        if (is_numeric($status)) {
            $this->addFilter('status',
                $this->getConnection()->quoteInto($this->_reviewTable.'.status_id=?', $status),
                'string');
        }
        elseif (is_string($status)) {
            $this->_sqlSelect->join($this->_reviewStatusTable,
                $this->_reviewTable.'.status_id='.$this->_reviewStatusTable.'.status_id');

            $this->addFilter('status',
                $this->getConnection()->quoteInto($this->_reviewStatusTable.'.status_code=?', $status),
                'string');
        }
        return $this;
    }

    public function setDateOrder($dir='DESC')
    {
        $this->setOrder('created_at', $dir);
        return $this;
    }

    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($item->getId())
                ->addRatingInfo()
                ->load();
            $item->setRatingVotes( $votesCollection );
        }

        return $this;
    }

    public function addReviewsTotalCount()
    {
        $this->_sqlSelect->joinLeft(array('r' => $this->_reviewTable), "{$this->_reviewTable}.entity_pk_value = r.entity_pk_value", 'COUNT(r.review_id) as total_reviews');
        $this->_sqlSelect->group("{$this->_reviewTable}.review_id");

        return $this;
    }
    /*
    public function load($a=false, $b=false)
    {
        parent::load(1);
    }
    */
}