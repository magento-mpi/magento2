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
     * Create history data from given object
     *
     * @param Varien_Object $reward
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function prepareFromObject(Varien_Object $object)
    {
        $this->setRewardId($object->getRewardId())
            ->setWebsiteId($object->getWebsiteId())
            ->setPointsBalance($object->getPointsBalance())
            ->setPointsDelta($object->getPointsDelta())
            ->setCurrencyAmount($object->getCurrencyAmount())
            ->setCurrencyDelta($object->getCurrencyDelta())
            ->setRateDescription($object->getRate()->getExchangeRateAsText())
            ->setAction($object->getAction())
            ->setComment($object->getComment());
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
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN:
                $addData['admin_user'] = Mage::getSingleton('admin/session')
                                ->getUser()->getName();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER:
                $addData['order_increment_id'] = $this->getOrderIncementId();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER:
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER:
                $addData['invitation_number'] = $this->getInvitationNumber();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG:
                $addData['tag'] = $this->getTag();
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA:
                $addData['order_increment_id'] = $this->getOrderIncementId();
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
                $messageVar = $this->getMessageVar('admin_user');
                $message = Mage::helper('enterprise_reward')->__('Updated points balance by Admin : %s', $messageVar);
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
                $messageVar = $this->getMessageVar('invitation_number');
                $message = Mage::helper('enterprise_reward')->__('Invitation %s converted into a Customer', $messageVar);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER:
                $messageVar = $this->getMessageVar('invitation_number');
                $message = Mage::helper('enterprise_reward')->__('Invitation %s converted into an Order', $messageVar);
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
}
