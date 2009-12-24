<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward history resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Mysql4_Reward_History extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward_history', 'history_id');
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);
        if (is_string($object->getData('additional_data'))) {
            $object->setData('additional_data', unserialize($object->getData('additional_data')));
        }
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Reward_Model_Mysql4_Reward_History
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);
        if (is_array($object->getData('additional_data'))) {
            $object->setData('additional_data', serialize($object->getData('additional_data')));
        }
        return $this;
    }

    /**
     * Check if history update with given action, customer and entity exist
     *
     * @param integer $customerId
     * @param integer $action
     * @param integer $websiteId
     * @param mixed $entity
     * @return boolean
     */
    public function isExistHistoryUpdate($customerId, $action, $websiteId, $entity)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('reward_table' => $this->getTable('enterprise_reward/reward')), array())
            ->joinInner(array('history_table' => $this->getMainTable()),
                'history_table.reward_id = reward_table.reward_id', array())
            ->where('history_table.action = ?', $action)
            ->where('history_table.website_id = ?', $websiteId)
            ->where('history_table.entity = ?', $entity)
            ->columns(array('history_table.history_id'));
        if ($this->_getReadAdapter()->fetchRow($select)) {
            return true;
        }
        return false;
    }

    /**
     * Return total quantity rewards for specified action and customer
     *
     * @param int $action
     * @param int $customerId
     * @param integer $websiteId
     * @return int
     */
    public function getTotalQtyRewards($action, $customerId, $websiteId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('history_table' => $this->getMainTable()), array('COUNT(*)'))
            ->joinInner(array('reward_table' => $this->getTable('enterprise_reward/reward')),
                'history_table.reward_id = reward_table.reward_id', array())
            ->where("history_table.action=?", $action)
            ->where("reward_table.customer_id=?", $customerId)
            ->where("history_table.website_id=?", $websiteId);

        return intval($this->_getReadAdapter()->fetchOne($select));
    }

    /**
     * Retrieve actual history records that have unused points, i.e. points_delta-points_used > 0
     *
     * @param Enterprise_Reward_Model_Reward_History $history
     * @param int $required Points total that required
     * @return array
     */
    public function getUnusedPoints($history, $required)
    {
        $required = (int)abs($required);
        if (!$required) {
            return array();
        }
        $this->getReadConnection()->multi_query('SET @available_total=0, @available=0;');
        $sql = "
            SELECT * , @available AS available, @available_total AS available_total
            FROM `{$this->getMainTable()}` AS h
            WHERE (@available := h.points_delta - h.points_used) > 0
                AND (@available_total := @available + @available_total)
                AND @available_total < {$required} + @available
                AND h.is_expired=0
                AND h.reward_id='{$history->getRewardId()}'
                AND h.website_id='{$history->getWebsiteId()}'
            ORDER BY h.history_id";
        return $this->_getReadAdapter()->fetchAll($sql);
    }

    /**
     * Update points_used field for non-used points
     *
     * @param Enterprise_Reward_Model_Reward_History $history
     * @param int $required Points total that required
     * @return Enterprise_Reward_Model_Mysql4_Reward_History
     */
    public function useAvailablePoints($history, $required)
    {
        $available = $this->getUnusedPoints($history, $required);
        if (!is_array($available) || count($available) == 0) {
            return $this;
        }

        $required = (int)abs($required);
        $sql = "UPDATE `{$this->getMainTable()}` SET `points_used` = CASE `history_id` ";
        $updates = 0;
        foreach ($available as $row) {
            if ($required <= 0) {
                break;
            }
            $rowAvailable = $row['points_delta'] - $row['points_used'];
            $pointsUsed = min($required, $rowAvailable);
            $required -= $pointsUsed;
            $newPointsUsed = $pointsUsed + $row['points_used'];
            $sql .= " WHEN '{$row['history_id']}' THEN '{$newPointsUsed}' ";
            $updates++;
        }
        $sql .= " ELSE `points_used` END ";
        if ($updates > 0) {
            $this->_getWriteAdapter()->query($sql);
        }
        return $this;
    }

    /**
     * Perform Row-level data update
     *
     * @param Enterprise_Reward_Model_Reward $reward
     * @param array $data New data
     * @return Enterprise_Reward_Model_Mysql4_Reward
     */
    public function updateHistoryRow(Enterprise_Reward_Model_Reward_History $object, $data)
    {
        if (!$object->getId() || !is_array($data)) {
            return $this;
        }
        $where = array($this->getIdFieldName().'=?' => $object->getId());
        $this->_getWriteAdapter()
            ->update($this->getMainTable(), $data, $where);
        return $this;
    }
}
