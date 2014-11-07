<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

/**
 * Class SaveRewardNotifications
 */
class SaveRewardNotifications
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_rewardData = $rewardData;
    }

    /**
     * Update reward notifications for customer
     *
     * @param \Magento\Framework\Event\Observer $subject
     * @return $this
     */
    public function execute($subject)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }

        $request = $subject->getEvent()->getRequest();
        /** @var \Magento\Customer\Api\Data\CustomerInterfaceBuilder $customerBuilder */
        $customerBuilder = $subject->getEvent()->getCustomer();

        /*
         * Customer builder was passed to event in order to provide possibility to observer to change
         * the data of the Customer Data Object.
         * Now we're constructing the Customer object from the builder in order to read the data
         * and populate Builder back with it.
         */
        $customer = $customerBuilder->create();
        $customerBuilder->populate($customer);

        $data = $request->getPost('reward');
        // If new customer
        if (!$customer->getId()) {
            $subscribeByDefault = (int)$this->_rewardData->getNotificationConfig(
                'subscribe_by_default',
                (int)$customer->getWebsiteId()
            );
            $data['reward_update_notification'] = $subscribeByDefault;
            $data['reward_warning_notification'] = $subscribeByDefault;
        }

        $customerBuilder->setCustomAttribute(
            'reward_update_notification',
            empty($data['reward_update_notification']) ? 0 : 1
        );
        $customerBuilder->setCustomAttribute(
            'reward_warning_notification',
            empty($data['reward_warning_notification']) ? 0 : 1
        );

        return $this;
    }
}
