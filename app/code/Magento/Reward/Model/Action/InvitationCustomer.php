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
namespace Magento\Reward\Model\Action;

class InvitationCustomer extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param array $data
     */
    public function __construct(\Magento\Reward\Helper\Data $rewardData, array $data = array())
    {
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
        return (int)$this->_rewardData->getPointsConfig('invitation_customer', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $invitation = $this->getEntity();
        if ($invitation->getData('status') != \Magento\Invitation\Model\Invitation::STATUS_ACCEPTED) {
            return false;
        }
        return !$this->isRewardLimitExceeded();
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return $this->_rewardData->getPointsConfig('invitation_customer_limit', $this->getReward()->getWebsiteId());
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
     * @param \Magento\Object $entity
     * @return \Magento\Reward\Model\Action\AbstractAction
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array('email' => $this->getEntity()->getEmail()));
        return $this;
    }
}
