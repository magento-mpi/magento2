<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter queue collection.
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Resource_Queue_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * True when subscribers info joined
     *
     * @var bool
     */
    protected $_addSubscribersFlag   = false;

    /**
     * True when filtered by store
     *
     * @var bool
     */
    protected $_isStoreFilter        = false;

    /**
     * Initializes collection
     *
     */
    protected function _construct()
    {
        $this->_map['fields']['queue_id'] = 'main_table.queue_id';
        $this->_init('Magento_Newsletter_Model_Queue', 'Magento_Newsletter_Model_Resource_Queue');
    }

    /**
     * Joines templates information
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addTemplateInfo()
    {
        $this->getSelect()->joinLeft(array('template'=>$this->getTable('newsletter_template')),
            'template.template_id=main_table.template_id',
            array('template_subject','template_sender_name','template_sender_email')
        );
        $this->_joinedTables['template'] = true;
        return $this;
    }

    /**
     * Adds subscribers info to selelect
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    protected function _addSubscriberInfoToSelect()
    {
        /** @var $select Magento_DB_Select */
        $select = $this->getConnection()->select()
            ->from(array('qlt' => $this->getTable('newsletter_queue_link')), 'COUNT(qlt.queue_link_id)')
            ->where('qlt.queue_id = main_table.queue_id');
        $totalExpr = new Zend_Db_Expr(sprintf('(%s)', $select->assemble()));
        $select = $this->getConnection()->select()
            ->from(array('qls' => $this->getTable('newsletter_queue_link')), 'COUNT(qls.queue_link_id)')
            ->where('qls.queue_id = main_table.queue_id')
            ->where('qls.letter_sent_at IS NOT NULL');
        $sentExpr  = new Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $this->getSelect()->columns(array(
            'subscribers_sent'  => $sentExpr,
            'subscribers_total' => $totalExpr
        ));
        return $this;
    }

    /**
     * Adds subscribers info to select and loads collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->_addSubscribersFlag && !$this->isLoaded()) {
            $this->_addSubscriberInfoToSelect();
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Joines subscribers information
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addSubscribersInfo()
    {
        $this->_addSubscribersFlag = true;
        return $this;
    }

    /**
     * Checks if field is 'subscribers_total', 'subscribers_sent'
     * to add specific filter or adds reguler filter
     *
     * @param string $field
     * @param mixed $condition
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, array('subscribers_total', 'subscribers_sent'))) {
            $this->addFieldToFilter('main_table.queue_id', array('in'=>$this->_getIdsFromLink($field, $condition)));
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Returns ids from queue_link table
     *
     * @param string $field
     * @param mixed $condition
     * @return array
     */
    protected function _getIdsFromLink($field, $condition)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getTable('newsletter_queue_link'),
                array('queue_id', 'total' => new Zend_Db_Expr('COUNT(queue_link_id)'))
            )
            ->group('queue_id')
            ->having($this->_getConditionSql('total', $condition));

        if ($field == 'subscribers_sent') {
            $select->where('letter_sent_at IS NOT NULL');
        }

        $idList = $this->getConnection()->fetchCol($select);

        if (count($idList)) {
            return $idList;
        }

        return array(0);
    }

    /**
     * Set filter for queue by subscriber.
     *
     * @param int $subscriberId
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addSubscriberFilter($subscriberId)
    {
        $this->getSelect()->join(array('link'=>$this->getTable('newsletter_queue_link')),
            'main_table.queue_id=link.queue_id',
            array('letter_sent_at')
        )
        ->where('link.subscriber_id = ?', $subscriberId);

        return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()
            ->where('main_table.queue_status in (?)', array(Magento_Newsletter_Model_Queue::STATUS_SENDING,
                                                            Magento_Newsletter_Model_Queue::STATUS_NEVER))
            ->where('main_table.queue_start_at < ?', Mage::getSingleton('Magento_Core_Model_Date')->gmtdate())
            ->where('main_table.queue_start_at IS NOT NULL');

        return $this;
    }

    /**
     * Add filter by only not sent items
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addOnlyUnsentFilter()
    {
        $this->addFieldToFilter('main_table.queue_status', Magento_Newsletter_Model_Queue::STATUS_NEVER);

           return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function addStoreFilter($storeIds)
    {
        if (!$this->_isStoreFilter) {
            $this->getSelect()->joinInner(array('store_link' => $this->getTable('newsletter_queue_store_link')),
                'main_table.queue_id = store_link.queue_id', array()
            )
            ->where('store_link.store_id IN (?)', $storeIds)
            ->group('main_table.queue_id');
            $this->_isStoreFilter = true;
        }
        return $this;
    }
}
