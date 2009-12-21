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
 * Reward history model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Reward_History extends Mage_Core_Model_Abstract
{
    protected $_reward = null;
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward_history');
    }

    /**
     * Processing object before save data.
     * Prepare history data
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    protected function _beforeSave()
    {
        if ($this->getWebsiteId()) {
            $this->setBaseCurrencyCode(
                Mage::app()->getWebsite($this->getWebsiteId())->getBaseCurrencyCode()
            );
        }
        return parent::_beforeSave();
    }

    /**
     * Setter
     *
     * @param Enterprise_Reward_Model_Reward $reward
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function setReward($reward)
    {
        $this->_reward = $reward;
        return $this;
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        return $this->_reward;
    }

    /**
     * Create history data from reward object
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function prepareFromReward()
    {
        $store = $this->getReward()->getStore();
        if ($store === null) {
            $store = Mage::app()->getStore();
        }
        $this->setRewardId($this->getReward()->getId())
            ->setWebsiteId($this->getReward()->getWebsiteId())
            ->setStoreId($store->getId())
            ->setPointsBalance($this->getReward()->getPointsBalance())
            ->setPointsDelta($this->getReward()->getPointsDelta())
            ->setCurrencyAmount($this->getReward()->getCurrencyAmount())
            ->setCurrencyDelta($this->getReward()->getCurrencyDelta())
            ->setRateDescription($this->getReward()->getRateToCurrency()->getExchangeRateAsText())
            ->setAction($this->getReward()->getAction())
            ->setComment($this->getReward()->getComment());
        return $this;
    }

    /**
     * Getter.
     * Unserialize if need
     *
     * @return array
     */
    public function getAdditionalData()
    {
        if (is_string($this->getData('additional_data'))) {
            $this->setData('additional_data', unserialize($this->getData('additional_data')));
        }
        return $this->getData('additional_data');
    }

    /**
     * Add additional values to additional_data
     *
     * @param array $data
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function addAdditionalData($data)
    {
        if (is_array($data)) {
            $additional = $this->getDataSetDefault('additional_data', array());
            foreach ($data as $k => $v) {
                $additional[$k] = $v;
            }
            $this->setData('additional_data', $additional);
        }

        return $this;
    }

    /**
     * Retrieve translated and prepared message
     *
     * @return string
     */
    public function getMessage()
    {
        if (!$this->hasData('message')) {
            $action = Mage::getSingleton('enterprise_reward/reward')->getActionInstance($this->getAction());
            $message = '';
            if ($action !== null) {
                $message = $action->getHistoryMessage($this->getAdditionalData());
            }
            $this->setData('message', $message);
        }
        return $this->_getData('message');
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
        $result = $this->_getResource()->isExistHistoryUpdate($customerId, $action, $websiteId, $entity);
        return $result;
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
        return $this->_getResource()->getTotalQtyRewards($action, $customerId, $websiteId);
    }
}
