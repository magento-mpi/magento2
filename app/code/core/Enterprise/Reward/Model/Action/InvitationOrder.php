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
 * Reward action to add points to inviter when his referral purchases order
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Action_InvitationOrder extends Enterprise_Reward_Model_Action_Abstract
{
    /**
     * Getter for invitation instance by order
     *
     * @return Varien_Object
     */
    protected function _getInvitation()
    {
        if (!$this->hasData('invitation')) {
            $order = $this->getEntity();
            $invitation = Mage::getModel('enterprise_invitation/invitation')
                ->load($order->getCustomerId(), 'referral_id');
            $this->setData('invitation', $invitation);
        }
        return $this->_getData('invitation');
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $invitation = $this->_getInvitation();
        if (!$invitation->getId() || !$invitation->getCustomerId()) {
            return false;
        }

        return $this->isRewardLimitExceeded();
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return Mage::helper('enterprise_reward')->getPointsConfig('invitation_order_limit', $this->getReward()->getWebsiteId());
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $email = isset($args['email']) ? $args['email'] : '';
        $incrementId = isset($args['order_increment_id']) ? $args['order_increment_id'] : '';
        return Mage::helper('enterprise_reward')->__('Invitation to %s converted into an Order #%s', $email, $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Varien_Object $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $invitation = $this->_getInvitation();
        $this->getHistory()->addAdditionalData(array(
            'order_increment_id' => $this->getEntity()->getOrderIncrementId(),
            'email' => $invitation->getEmail()
        ));
        return $this;
    }
}
