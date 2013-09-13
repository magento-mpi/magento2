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
 * Reward action for Newsletter Subscription
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Action_Newsletter extends Magento_Reward_Model_Action_Abstract
{
    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Reward_Helper_Data $rewardData
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
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
        return (int)$this->_rewardData->getPointsConfig('newsletter', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $subscriber = $this->getEntity();
        $subscriberStatuses = array(
            Magento_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED,
            Magento_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED
        );
        if (!in_array($subscriber->getData('subscriber_status'), $subscriberStatuses)) {
            return false;
        }

        /* @var $subscribers Magento_Newsletter_Model_Resource_Subscriber_Collection */
        $subscribers = Mage::getResourceModel('Magento_Newsletter_Model_Resource_Subscriber_Collection')
            ->addFieldToFilter('customer_id', $subscriber->getCustomerId())
            ->load();
        // check for existing customer subscribtions
        $found = false;
        foreach ($subscribers as $item) {
            if ($subscriber->getSubscriberId() != $item->getSubscriberId()) {
                $found = true;
                break;
            }
        }
        $exceeded = $this->isRewardLimitExceeded();
        return !$found && !$exceeded;
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
        return __('Signed up for newsletter with email %1', $email);
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
