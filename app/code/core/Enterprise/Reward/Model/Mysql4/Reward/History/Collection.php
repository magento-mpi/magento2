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
 * Reward history collection
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Mysql4_Reward_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_expiryConfig = array();

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward_history');
    }

    /**
     * Join reward table and retrieve total balance total with customer_id
     *
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    protected function _joinReward()
    {
        if ($this->getFlag('reward_joined')) {
            return $this;
        }
        $this->getSelect()->joinInner(
            array('reward_table' => $this->getTable('enterprise_reward/reward')),
            'reward_table.reward_id = main_table.reward_id',
            array('customer_id', 'points_balance_total' => 'points_balance')
        );
        $this->setFlag('reward_joined', true);
        return $this;
    }

    /**
     * Getter for $_expiryConfig
     *
     * @param int $websiteId Specified Website Id
     * @return array|Varien_Object
     */
    protected function _getExpiryConfig($websiteId = null)
    {
        if ($websiteId !== null && isset($this->_expiryConfig[$websiteId])) {
            return $this->_expiryConfig[$websiteId];
        }
        return $this->_expiryConfig;
    }

    /**
     * Setter for $_expiryConfig
     *
     * @param array $config
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function setExpiryConfig($config)
    {
        if (!is_array($config)) {
            return $this;
        }
        $this->_expiryConfig = $config;
        return $this;
    }

    /**
     * Join reward table to filter history by customer id
     *
     * @param string $customerId
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function addCustomerFilter($customerId)
    {
        if ($customerId) {
            $this->_joinReward();
            $this->getSelect()->where('reward_table.customer_id = ?', $customerId);
        }
        return $this;
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId);
        return $this;
    }

    /**
     * Join additional customer information, such as email, name etc.
     *
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function addCustomerInfo()
    {
        if ($this->getFlag('customer_added')) {
            return $this;
        }

        $this->_joinReward();

        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');

        $connection = $this->getConnection();
        /* @var $connection Zend_Db_Adapter_Abstract */

        $this->getSelect()
            ->joinInner(
                array('ce' => $customer->getAttribute('email')->getBackend()->getTable()),
                'ce.entity_id=reward_table.customer_id',
                array('customer_email' => 'email')
             )
            ->joinLeft(
                array('clt' => $lastname->getBackend()->getTable()),
                $connection->quoteInto('clt.entity_id=reward_table.customer_id AND clt.attribute_id = ?', $lastname->getAttributeId()),
                array('customer_lastname' => 'value')
             )
             ->joinLeft(
                array('cft' => $firstname->getBackend()->getTable()),
                $connection->quoteInto('cft.entity_id=reward_table.customer_id AND cft.attribute_id = ?', $firstname->getAttributeId()),
                array('customer_firstname' => 'value')
             );

        $this->setFlag('customer_added', true);
        return $this;
    }

    /**
     * Add correction to expiration date based on expiry calculation
     *
     * @param int $websiteId
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function addExpirationDate($websiteId = null)
    {
        $expiryConfig = $this->_getExpiryConfig($websiteId);
        if (!$expiryConfig) {
            return $this;
        }

        if ($websiteId !== null) {
            $field = $expiryConfig->getExpiryCalculation()== 'static' ? 'expired_at_static' : 'expired_at_dynamic';
            $this->getSelect()->columns(array('expiration_date' => $field));
        } else {
            $sql = " CASE main_table.website_id ";
            $cases = array();
            foreach ($expiryConfig as $wId => $config) {
                $field = $config->getExpiryCalculation()== 'static' ? 'expired_at_static' : 'expired_at_dynamic';
                $cases[] = " WHEN '{$wId}' THEN `{$field}` ";
            }
            if (count($cases) > 0) {
                $sql .= implode(' ', $cases) . ' END ';
                $this->getSelect()->columns( array('expiration_date' => new Zend_Db_Expr($sql)) );
            }
        }

        return $this;
    }

    /**
     * Return amounts of points that will be expired in {$inDays} aggregated by customer and website
     *
     * @param int $websiteId // Days before points will be marked as expired
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function loadExpiredSoonPoints($websiteId)
    {
        $expirationType = Mage::helper('enterprise_reward')->getGeneralConfig('expiry_calculation', $websiteId);
        $field = $expirationType == 'static' ? 'expired_at_static' : 'expired_at_dynamic';
        $inDays = (int)Mage::helper('enterprise_reward')->getNotificationConfig('expiry_day_before');
        $now = $this->getResource()->formatDate(time());
        $this->getSelect()
            ->where('points_delta-points_used>0 AND is_expired=0')
            ->columns( array('total_expired' => new Zend_Db_Expr('SUM(points_delta-points_used)')) )
            ->group(array('reward_table.customer_id', 'main_table.website_id'))
            ->having("ADDDATE(expiration_date, INTERVAL -{$inDays} DAY) < '{$now}'")
            ->order(array('reward_table.customer_id', 'main_table.website_id'));

        return $this;
    }

    /**
     * Return all records that shoul be expired now
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function loadPointsForExpiration()
    {
        $this->addCustomerInfo()
            ->addExpirationDate();

        $now = $this->getResource()->formatDate(time());
        $this->getSelect()
            ->where('is_expired=0')
                ->having("expiration_date < '{$now}'")
            ->order('main_table.history_id');

        return $this;
    }
}
