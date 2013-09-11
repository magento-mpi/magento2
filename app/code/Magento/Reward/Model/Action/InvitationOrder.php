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
 * Reward action to add points to inviter when his referral purchases order
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

class InvitationOrder extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)\Mage::helper('Magento\Reward\Helper\Data')->getPointsConfig('invitation_order', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $frequency = \Mage::helper('Magento\Reward\Helper\Data')->getPointsConfig(
            'invitation_order_frequency', $this->getReward()->getWebsiteId()
        );
        if ($frequency == '*') {
            return !($this->isRewardLimitExceeded());
        } else {
            return parent::canAddRewardPoints();
        }
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return \Mage::helper('Magento\Reward\Helper\Data')->getPointsConfig(
            'invitation_order_limit',
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
        return __('The invitation to %1 converted into an order.', $email);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param \Magento\Invitation\Model\Invitation $entity
     * @return \Magento\Reward\Model\Action\AbstractAction
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array('email' => $this->getEntity()->getEmail()));
        return $this;
    }
}
