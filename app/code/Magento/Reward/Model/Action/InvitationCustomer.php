<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action to add points to inviter when his referral becomes customer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Action_InvitationCustomer extends Magento_Reward_Model_Action_Abstract
{
    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)Mage::helper('Magento_Reward_Helper_Data')->getPointsConfig('invitation_customer', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $invitation = $this->getEntity();
        if ($invitation->getData('status') != Magento_Invitation_Model_Invitation::STATUS_ACCEPTED) {
            return false;
        }
        return !($this->isRewardLimitExceeded());
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return Mage::helper('Magento_Reward_Helper_Data')->getPointsConfig(
            'invitation_customer_limit',
            $this->getReward()->getWebsiteId()
        );
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
        return __('The invitation to %1 converted into a customer.', $email);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Magento_Object $entity
     * @return Magento_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array(
            'email' => $this->getEntity()->getEmail()
        ));
        return $this;
    }
}
