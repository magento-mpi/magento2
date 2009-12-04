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
        $this->_prepareAdditionalInfo();
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
            ->setRate($object->getRate())
            ->setAction($object->getAction())
            ->setComment($object->getComment());
        return $this;
    }

    /**
     * Prepare additional information (as text)
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    protected function _prepareAdditionalInfo()
    {
        $addInfo = $this->_retrieveMessageByAction($this->getAction());
        $this->setData('additional_info', $addInfo);
        return $this;
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
                $adminUser = Mage::getSingleton('admin/session')
                    ->getUser()->getName();
                $message = Mage::helper('enterprise_reward')->__('Updated points balance by Admin : %s', $adminUser);
                if ($this->getComment()) {
                    $message .= '( ' . $this->getComment() . ' )';
                }
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER:
                $orderIncrementId = $this->getOrderIncementId();
                $message = Mage::helper('enterprise_reward')->__('Redeemed for Order : #%s', $orderIncrementId);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_REGISTER:
                $message = Mage::helper('enterprise_reward')->__('Registered at Website');
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_NEWSLETTER:
                $message = Mage::helper('enterprise_reward')->__('Signed up for Newsletter');
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_CUSTOMER:
                $invitation = $this->getInvitationNumber();
                $message = Mage::helper('enterprise_reward')->__('Invitation %s converted into a Customer', $invitation);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_INVITATION_ORDER:
                $invitation = $this->getInvitationNumber();
                $message = Mage::helper('enterprise_reward')->__('Invitation %s converted into an Order', $invitation);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_REVIEW:
                $message = Mage::helper('enterprise_reward')->__('Submitted Review passed Moderation');
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG:
                $tag = $this->getTag();
                $message = Mage::helper('enterprise_reward')->__('Submitted Tag (%s) approved by Moderator', $tag);
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER_EXTRA:
                $orderIncrementId = $this->getOrderIncementId();
                $message = Mage::helper('enterprise_reward')->__('Gained Promotion Extra Points from Order #%s', $orderIncrementId);
                break;
        }
        return $message;
    }
}