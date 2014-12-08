<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource;

use Magento\Reward\Model\Reward as ModelReward;

/**
 * Reward resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Reward extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_reward', 'reward_id');
    }

    /**
     * Fetch reward by customer and website and set data to reward object
     *
     * @param ModelReward $reward
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function loadByCustomerId(ModelReward $reward, $customerId, $websiteId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->where(
            'customer_id = :customer_id'
        )->where(
            'website_id = :website_id'
        );
        $bind = [':customer_id' => $customerId, ':website_id' => $websiteId];
        if ($data = $this->_getReadAdapter()->fetchRow($select, $bind)) {
            $reward->addData($data);
        }
        $this->_afterLoad($reward);
        return $this;
    }

    /**
     * Perform Row-level data update
     *
     * @param ModelReward $object
     * @param array $data New data
     * @return $this
     */
    public function updateRewardRow(ModelReward $object, $data)
    {
        if (!$object->getId() || !is_array($data)) {
            return $this;
        }
        $where = [$this->getIdFieldName() . '=?' => $object->getId()];
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        return $this;
    }

    /**
     * Prepare orphan points by given website id and website base currency code
     * after website was deleted
     *
     * @param int $websiteId
     * @param string $baseCurrencyCode
     * @return $this
     */
    public function prepareOrphanPoints($websiteId, $baseCurrencyCode)
    {
        $adapter = $this->_getWriteAdapter();
        if ($websiteId) {
            $adapter->update(
                $this->getMainTable(),
                ['website_id' => null, 'website_currency_code' => $baseCurrencyCode],
                ['website_id = ?' => $websiteId]
            );
        }
        return $this;
    }

    /**
     * Delete orphan (points of deleted website) points by given customer
     *
     * @param int $customerId
     * @return $this
     */
    public function deleteOrphanPointsByCustomer($customerId)
    {
        if ($customerId) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                ['customer_id = ?' => $customerId, new \Zend_Db_Expr('website_id IS NULL')]
            );
        }
        return $this;
    }

    /**
     * Save salesrule reward points delta
     *
     * @param int $ruleId
     * @param int $pointsDelta
     * @return void
     */
    public function saveRewardSalesrule($ruleId, $pointsDelta)
    {
        $select = $this->_getWriteAdapter()->insertOnDuplicate(
            $this->getTable('magento_reward_salesrule'),
            ['rule_id' => $ruleId, 'points_delta' => $pointsDelta],
            ['points_delta']
        );
    }

    /**
     * Retrieve reward salesrule data by given rule Id or array of Ids
     *
     * @param int|array $rule
     * @return array
     */
    public function getRewardSalesrule($rule)
    {
        $data = [];
        $select = $this->_getReadAdapter()->select()->from(
            $this->getTable('magento_reward_salesrule')
        )->where(
            'rule_id IN (?)',
            $rule
        );
        if (is_array($rule)) {
            $data = $this->_getReadAdapter()->fetchAll($select);
        } else {
            $data = $this->_getReadAdapter()->fetchRow($select);
        }
        return $data;
    }
}
