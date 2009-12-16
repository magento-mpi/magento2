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
        $this->prepareAdditionalData();
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
        $this->setRewardId($this->getReward()->getId())
            ->setWebsiteId($this->getReward()->getWebsiteId())
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
     * Prepare additional data
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function prepareAdditionalData()
    {
        $addData = $this->_retrieveAdditionalDataByAction($this->getAction());
        $this->setData('additional_data', $addData);
        return $this;
    }

    /**
     * Retrieve prepared additional data by action
     *
     * @param integer $action
     * @return array
     */
    protected function _retrieveAdditionalDataByAction($action)
    {
        $addData = array();
        switch ($action) {
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER:
                $addData['order_increment_id'] = $this->getReward()->getOrderIncrementId();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER:
                $addData['invitation_email'] = $this->getReward()->getInvitation()->getEmail();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER:
                $addData['order_increment_id'] = $this->getReward()->getOrderIncrementId();
                $addData['invitation_email'] = $this->getReward()->getInvitation()->getEmail();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG:
                $addData['tag'] = $this->getReward()->getTag()->getName();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA:
                $addData['order_increment_id'] = $this->getReward()->getOrderIncrementId();
                break;
        }
        return $addData;
    }

    /**
     * Retrieve translated and prepared message
     *
     * @return string
     */
    public function getMessage()
    {
        if (!$this->getData('message')) {
            $message = $this->_retrieveMessageByAction($this->getAction());
            $this->setData('message', $message);
        }
        return $this->getData('message');
    }

    /**
     * Retrieve history message by given action
     *
     * @param integer $action
     * @return string
     */
    protected function _retrieveMessageByAction($action)
    {
        $message = '';
        switch ($action) {
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN:
                $message = Mage::helper('enterprise_reward')->__('Updated by Moderator');
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER:
                $messageVar = $this->getMessageVar('order_increment_id');
                $message = Mage::helper('enterprise_reward')->__('Redeemed for Order : #%s', $messageVar);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_REGISTER:
                $message = 'Registered at Website';
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_NEWSLETTER:
                $message = 'Signed up for Newsletter';
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER:
                $messageVar = $this->getMessageVar('invitation_email');
                $message = Mage::helper('enterprise_reward')->__('Invitation to %s converted into a Customer', $messageVar);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER:
                $messageVar = $this->getMessageVar('invitation_email');
                $message = Mage::helper('enterprise_reward')->__('Invitation to %s converted into an Order', $messageVar);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_REVIEW:
                $message = 'Submitted Review passed Moderation';
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG:
                $messageVar = $this->getMessageVar('tag');
                $message = Mage::helper('enterprise_reward')->__('Submitted Tag (%s) approved by Moderator', $messageVar);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA:
                $messageVar = $this->getMessageVar('order_increment_id');
                $message = Mage::helper('enterprise_reward')->__('Gained Promotion Extra Points from Order #%s', $messageVar);
                break;
        }
        return $message;
    }

    /**
     * Retrieve message var form additional data
     *
     * @param string $varName
     * @return null | string
     */
    public function getMessageVar($varName)
    {
        $additionalData = $this->getAdditionalData();
        if (is_array($additionalData) && isset($additionalData[$varName])) {
            return $additionalData[$varName];
        }
        return null;
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
}
