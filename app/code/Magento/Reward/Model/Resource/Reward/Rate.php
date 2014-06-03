<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward;

use Magento\Reward\Model\Reward\Rate as RewardRate;

/**
 * Reward rate resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rate extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_reward_rate', 'rate_id');
    }

    /**
     * Fetch rate customer group and website
     *
     * @param RewardRate $rate
     * @param int $customerGroupId
     * @param int $websiteId
     * @param int $direction
     * @return $this
     */
    public function fetch(RewardRate $rate, $customerGroupId, $websiteId, $direction)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->where(
            'website_id IN (:website_id, 0)'
        )->where(
            'customer_group_id IN (:customer_group_id, 0)'
        )->where(
            'direction = :direction'
        )->order(
            'customer_group_id DESC'
        )->order(
            'website_id DESC'
        )->limit(
            1
        );

        $bind = array(
            ':website_id' => (int)$websiteId,
            ':customer_group_id' => (int)$customerGroupId,
            ':direction' => $direction
        );

        $row = $this->_getReadAdapter()->fetchRow($select, $bind);
        if ($row) {
            $rate->addData($row);
        }

        $this->_afterLoad($rate);
        return $this;
    }

    /**
     * Retrieve rate data bu given params or empty array if rate with such params does not exists
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $direction
     * @return array
     */
    public function getRateData($websiteId, $customerGroupId, $direction)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->where(
            'website_id = :website_id'
        )->where(
            'customer_group_id = :customer_group_id'
        )->where(
            'direction = :direction'
        );
        $bind = array(
            ':website_id' => (int)$websiteId,
            ':customer_group_id' => (int)$customerGroupId,
            ':direction' => $direction
        );
        $data = $this->_getReadAdapter()->fetchRow($select, $bind);
        if ($data) {
            return $data;
        }

        return array();
    }
}
