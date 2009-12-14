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
 * Reward observer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Observer
{

    /**
     * Prepare reward points data to update
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function prepareRewardPointsToSave($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return;
        }
        $request = $observer->getRequest();
        if ($data = $request->getPost('reward')) {
            $customer = $observer->getEvent()->getCustomer();
            $customer->setRewardPointsData($data)
                ->setRewardUpdateNotification((isset($data['reward_update_notification'])?true:false))
                ->setRewardWarningNotification((isset($data['reward_warning_notification'])?true:false));
        }
        return $this;
    }

    /**
     * Update reward points after customer register
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function saveRewardPoints($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return;
        }
        if ($data = $observer->getEvent()->getCustomer()->getRewardPointsData()) {
            if (!empty($data['points_delta'])) {
                $reward = Mage::getModel('enterprise_reward/reward')
                    ->setData($data)
                    ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                    ->setCustomer($observer->getEvent()->getCustomer())
                    ->updateRewardPoints();

                // send notifications
                $reward->sendBalanceUpdateNotification()
                    ->sendBalanceWarningNotification();

            }
        }
        return $this;
    }

    /**
     * Update reward points after customer register
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function customerRegister($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        if ($customer->isObjectNew()) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomer($customer)
                ->setStore(Mage::app()->getStore()->getId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_REGISTER)
                ->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after review submit
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function reviewSubmit($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $review Mage_Review_Model_Review */
        $review = $observer->getEvent()->getObject();
        if ($review->isApproved() && $review->getCustomerId()) {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($review->getCustomerId())
                ->setStore($review->getStoreId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_REVIEW)
                ->setReview($review)
                ->updateRewardPoints();
        }
        return $this;
    }

    /**
     * Update points balance after tag submit
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Reward_Model_Observer
     */
    public function tagSubmit($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $tag Mage_Tag_Model_Tag */
        $tag = $observer->getEvent()->getObject();
        /**
         * to remove
         */
        $tag->setCustomerId(1);
        if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getCustomerId()) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($tag->getCustomerId())
                ->setStore($tag->getStoreId())
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG)
                ->setTag($tag)
                ->updateRewardPoints();
        }
        return $this;
    }
}

