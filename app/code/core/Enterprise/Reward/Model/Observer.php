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
     */
    public function prepareRewardPointsToSave($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return;
        }
        $request = $observer->getRequest();
        if ($data = $request->getPost('reward')) {
            $customer = $observer->getCustomer();
            $customer->setRewardPointsData($data)
                ->setRewardUpdateNotification((isset($data['reward_update_notification'])?true:false))
                ->setRewardWarningNotification((isset($data['reward_warning_notification'])?true:false));
        }
    }

    /**
     * Update reward points
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveRewardPoints($observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return;
        }
        if ($data = $observer->getCustomer()->getRewardPointsData()) {
            if (!empty($data['points_delta'])) {
                $reward = Mage::getModel('enterprise_reward/reward')
                    ->setData($data)
                    ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                    ->setCustomer($observer->getCustomer());
                $reward->save();
            }
        }
    }
}
