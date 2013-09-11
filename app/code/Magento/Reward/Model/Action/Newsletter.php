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
namespace Magento\Reward\Model\Action;

class Newsletter extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)\Mage::helper('Magento\Reward\Helper\Data')->getPointsConfig('newsletter', $websiteId);
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
            \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED,
            \Magento\Newsletter\Model\Subscriber::STATUS_UNCONFIRMED
        );
        if (!in_array($subscriber->getData('subscriber_status'), $subscriberStatuses)) {
            return false;
        }

        /* @var $subscribers \Magento\Newsletter\Model\Resource\Subscriber\Collection */
        $subscribers = \Mage::getResourceModel('Magento\Newsletter\Model\Resource\Subscriber\Collection')
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
     * @param \Magento\Object $entity
     * @return \Magento\Reward\Model\Action\AbstractAction
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
