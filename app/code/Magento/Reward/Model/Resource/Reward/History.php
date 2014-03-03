<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward;

use Magento\Reward\Model\Reward\History as ModelRewardHistory;

/**
 * Reward history resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Date time formatter
     *
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_reward_history', 'history_id');
        $this->_serializableFields = array(
            'additional_data' => array(array(), array()),
        );
    }

    /**
     * Check if history update with given action, customer and entity exist
     *
     * @param int $customerId
     * @param int $action
     * @param int $websiteId
     * @param mixed $entity
     * @return bool
     */
    public function isExistHistoryUpdate($customerId, $action, $websiteId, $entity)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(array('reward_table' => $this->getTable('magento_reward')), array())
            ->joinInner(array('history_table' => $this->getMainTable()),
                'history_table.reward_id = reward_table.reward_id', array())
            ->where('history_table.action = :action')
            ->where('history_table.website_id = :website_id')
            ->where('history_table.entity = :entity')
            ->columns(array('history_table.history_id'));
        $bind = array(
            'action'     => $action,
            'website_id' => $websiteId,
            'entity'     => $entity
        );
        if ($this->_getWriteAdapter()->fetchRow($select, $bind)) {
            return true;
        }
        return false;
    }

    /**
     * Return total quantity rewards for specified action and customer
     *
     * @param int $action
     * @param int $customerId
     * @param int $websiteId
     * @return int
     */
    public function getTotalQtyRewards($action, $customerId, $websiteId)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from(array('history_table' => $this->getMainTable()), array('COUNT(*)'))
            ->joinInner(array('reward_table' => $this->getTable('magento_reward')),
                'history_table.reward_id = reward_table.reward_id', array())
            ->where('history_table.action=:action')
            ->where('reward_table.customer_id=:customer_id')
            ->where('history_table.website_id=:website_id');
        $bind = array(
            'action'      => $action,
            'customer_id' => $customerId,
            'website_id'  => $websiteId
        );
        return intval($this->_getReadAdapter()->fetchOne($select, $bind));
    }

    /**
     * Retrieve actual history records that have unused points, i.e. points_delta-points_used > 0
     * Update points_used field for non-used points
     *
     * @param ModelRewardHistory $history
     * @param int $required Points total that required
     * @return $this
     * @throws \Exception
     */
    public function useAvailablePoints($history, $required)
    {
        $required = (int)abs($required);
        if (!$required) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        try {
            $adapter->beginTransaction();
            $select = $adapter->select()
                ->from(array('history' => $this->getMainTable()), array('history_id', 'points_delta', 'points_used'))
                ->where('reward_id = :reward_id')
                ->where('website_id = :website_id')
                ->where('is_expired=0')
                ->where('points_delta - points_used > 0')
                ->order('history_id')
                ->forUpdate(true);
            $bind = array(
                ':reward_id'  => $history->getRewardId(),
                ':website_id' => $history->getWebsiteId()
            );

            $stmt = $adapter->query($select, $bind);

            $updateSqlValues = array();
            $data = array();
            while ($row = $stmt->fetch()) {
                if ($required <= 0) {
                    break;
                }
                $rowAvailable = $row['points_delta'] - $row['points_used'];
                $pointsUsed = min($required, $rowAvailable);
                $required -= $pointsUsed;
                $newPointsUsed = $pointsUsed + $row['points_used'];
                $data[] = array(
                    'history_id' => $row['history_id'],
                    'points_used' => $newPointsUsed
                );
            }

            if (count($data) > 0) {
                $adapter->insertOnDuplicate($this->getMainTable(), $data, array('history_id', 'points_used'));
            }

            $adapter->commit();
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Update history expired_at_dynamic field for specified websites when config changed
     *
     * @param int $days Reward Points Expire in (days)
     * @param array $websiteIds Array of website ids that must be updated
     * @return $this
     */
    public function updateExpirationDate($days, $websiteIds)
    {
        $adapter = $this->_getWriteAdapter();
        $websiteIds = is_array($websiteIds) ? $websiteIds : array($websiteIds);
        $days = (int)abs($days);
        $update = array();
        if ($days) {
            $update['expired_at_dynamic'] = $adapter->getDateAddSql(
                'created_at', $days, \Magento\DB\Adapter\AdapterInterface::INTERVAL_DAY
            );
        } else {
            $update['expired_at_dynamic'] = new \Zend_Db_Expr('NULL');
        }
        $where = array('website_id IN (?)' => $websiteIds);
        $adapter->update($this->getMainTable(), $update, $where);
        return $this;
    }


    /**
     * Make points expired for specified website
     *
     * @param int $websiteId
     * @param string $expiryType Expiry calculation (static or dynamic)
     * @param int $limit Limitation for records expired selection
     * @return $this
     */
    public function expirePoints($websiteId, $expiryType, $limit)
    {
        $adapter = $this->_getWriteAdapter();
        $now = $this->dateTime->formatDate(time());
        $field = $expiryType == 'static' ? 'expired_at_static' : 'expired_at_dynamic';

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('website_id = :website_id')
            ->where("{$field} < :time_now")
            ->where("{$field} IS NOT NULL")
            ->where('is_expired=?', 0)
            ->where('points_delta-points_used > ?', 0)
            ->limit((int)$limit);
        $bind = array(
            ':website_id' => $websiteId,
            ':time_now'   => $now
        );
        $duplicates = array();
        $expiredAmounts = array();
        $expiredHistoryIds = array();
        $stmt = $adapter->query($select, $bind);
        while ($row = $stmt->fetch()) {
            $row['created_at'] = $now;
            $row['expired_at_static'] = null;
            $row['expired_at_dynamic'] = null;
            $row['is_expired'] = '1';
            $row['is_duplicate_of'] = $row['history_id'];
            $expiredHistoryIds[] = $row['history_id'];
            unset($row['history_id']);
            if (!isset($expiredAmounts[$row['reward_id']])) {
                $expiredAmounts[$row['reward_id']] = 0;
            }
            $expiredAmount = $row['points_delta'] - $row['points_used'];
            $row['points_delta'] = -$expiredAmount;
            $row['points_used'] = 0;
            $expiredAmounts[$row['reward_id']] += $expiredAmount;
            $duplicates[] = $row;
        }

        if (count($expiredHistoryIds) > 0) {
            // decrease points balance of rewards
            foreach ($expiredAmounts as $rewardId => $expired) {
                if ($expired == 0) {
                    continue;
                }
                $bind = array(
                    'points_balance' => $adapter->getCheckSql(
                        "points_balance > {$expired}",
                        "points_balance-{$expired}",
                        0
                    )
                );
                $where = array('reward_id=?' => $rewardId);
                $adapter->update($this->getTable('magento_reward'), $bind, $where);
            }

            // duplicate expired records
            $adapter->insertMultiple($this->getMainTable(), $duplicates);

            // update is_expired field (using history ids instead where clause for better performance)
            $adapter->update(
                $this->getMainTable(),
                array('is_expired' => '1'),
                array('history_id IN (?)' => $expiredHistoryIds)
            );
        }

        return $this;
    }

    /**
     * Mark history records as notification was sent to customer (about points expiration)
     *
     * @param array $ids
     * @return $this
     */
    public function markAsNotified($ids)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('notification_sent' => 1),
            array('history_id IN (?)' => $ids)
        );
        return $this;
    }

    /**
     * Perform Row-level data update
     *
     * @param ModelRewardHistory $object
     * @param array $data New data
     * @return $this
     */
    public function updateHistoryRow(ModelRewardHistory $object, $data)
    {
        if (!$object->getId() || !is_array($data)) {
            return $this;
        }
        $where = array($this->getIdFieldName() . '=?' => $object->getId());
        $this->_getWriteAdapter()
            ->update($this->getMainTable(), $data, $where);
        return $this;
    }
}
