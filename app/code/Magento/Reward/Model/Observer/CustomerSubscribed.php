<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CustomerSubscribed
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
    }

    /**
     * Update points balance after first successful subscribtion
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        /* @var $subscriber \Magento\Newsletter\Model\Subscriber */
        $subscriber = $observer->getEvent()->getSubscriber();
        // reward only new subscribtions
        if (!$subscriber->isObjectNew() || !$subscriber->getCustomerId()) {
            return $this;
        }
        $websiteId = $this->_storeManager->getStore($subscriber->getStoreId())->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        $reward = $this->_rewardFactory->create()->setCustomerId(
            $subscriber->getCustomerId()
        )->setStore(
            $subscriber->getStoreId()
        )->setAction(
            \Magento\Reward\Model\Reward::REWARD_ACTION_NEWSLETTER
        )->setActionEntity(
            $subscriber
        )->updateRewardPoints();

        return $this;
    }
}
