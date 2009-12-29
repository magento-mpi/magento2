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
     * Update points_used field for non-used points
     *
     * @param Enterprise_Reward_Model_Reward_History $history
     * @param int $required Points total that required
     * @return Enterprise_Reward_Model_Mysql4_Reward_History
     */
    public function useAvailablePoints($history, $required)
    {
        $required = (int)abs($required);
        if (!$required) {
            return $this;
        }

        try {
            $this->_getWriteAdapter()->beginTransaction();
            $select = $this->_getReadAdapter()->select()
                ->from(array('history' => $this->getMainTable()), array('history_id', 'points_delta', 'points_used'))
                ->where('reward_id=?', $history->getRewardId())
                ->where('website_id=?', $history->getWebsiteId())
                ->where('is_expired=0')
                ->where('`points_delta`-`points_used`>0')
                ->order('history_id')
                ->forUpdate(true);

            $stmt = $this->_getReadAdapter()->query($select);
            $updateSql = "INSERT INTO `{$this->getMainTable()}` (`history_id`, `points_used`) VALUES ";
            $updateSqlValues = array();
            while ($row = $stmt->fetch()) {
                if ($required <= 0) {
                    break;
                }
                $rowAvailable = $row['points_delta'] - $row['points_used'];
                $pointsUsed = min($required, $rowAvailable);
                $required -= $pointsUsed;
                $newPointsUsed = $pointsUsed + $row['points_used'];
                $updateSqlValues[] = " ('{$row['history_id']}', '{$newPointsUsed}') ";
            }
            if (count($updateSqlValues) > 0) {
                $updateSql = $updateSql
                           . implode(',', $updateSqlValues)
                           . " ON DUPLICATE KEY UPDATE `points_used`=VALUES(`points_used`) ";
                $this->_getWriteAdapter()->query($updateSql);
            }
            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Update history expired_at_dynamic field for specified websites
     *
     * @param int $days Reward Points Expire in (days)
     * @param array $websiteIds Array of website ids that must be updated
     * @return Enterprise_Reward_Model_Mysql4_Reward_History
     */
    public function updateExpirationDate($days, $websiteIds)
    {
        $websiteIds = is_array($websiteIds) ? $websiteIds : array($websiteIds);
        $days = (int)abs($days);
        if ($days) {
            $newValue = "ADDDATE(`created_at`, INTERVAL {$days} DAY)";
        } else {
            $newValue = "NULL";
        }
        $sql = "UPDATE `{$this->getMainTable()}` SET `expired_at_dynamic`={$newValue} WHERE ";
        $sql.= $this->_getWriteAdapter()->quoteInto("`website_id` in (?)", $websiteIds);
        $this->_getWriteAdapter()->query($sql);
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
