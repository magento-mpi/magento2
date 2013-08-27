<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param array $data
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $rewardData,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($data);
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)$this->_rewardData->getPointsConfig('invitation_order', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $frequency = $this->_rewardData->getPointsConfig(
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
        return $this->_rewardData->getPointsConfig(
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
     * @param Enterprise_Invitation_Model_Invitation $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array('email' => $this->getEntity()->getEmail()));
        return $this;
    }
}
