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
        if (!$this->getFlag('reward_table_joined')) {
            $this->getSelect()->joinInner(
                array('reward_table' => $this->getTable('enterprise_reward/reward')),
                'reward_table.reward_id = main_table.reward_id',
                array('customer_id', 'points_balance_total' => 'points_balance')
            );
            $this->setFlag('reward_table_joined', true);
        }
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

        return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function loadExpiredSoonRecords()
    {
        $this->addCustomerInfo();
        $this->getSelect()->where('notification_sent=0 AND main_table.points_delta>0');
        $websites = Mage::app()->getWebsites();
        $websiteConditions = array();
        foreach (Mage::app()->getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */
            $expirationDays = (int)$website->getConfig('enterprise_reward/general/expiration_days');
            $sendWarningDaysBefore = (int)$website->getConfig('enterprise_reward/notification/expiry_day_before');
            if (!$expirationDays || !$sendWarningDaysBefore || $sendWarningDaysBefore > $expirationDays) {
                continue;
            }
            $expirationSeconds = 24 * 60 * 60 * $expirationDays;
            $sendWarningDaysBeforeSeconds = 24 * 60 * 60 * $sendWarningDaysBefore;
            $websiteConditions[] = sprintf('( (UNIX_TIMESTAMP(main_table.created_at) + %d - %d <= %d) OR (UNIX_TIMESTAMP(main_table.expired_at) - %d <= %d) )',
                $expirationSeconds,
                time(),
                $sendWarningDaysBeforeSeconds,
                time(),
                $sendWarningDaysBeforeSeconds
            );
        }

        if (count($websiteConditions)) {
            $this->getSelect()->where(implode(' OR ', $websiteConditions));
        }

        return $this;
    }
}
