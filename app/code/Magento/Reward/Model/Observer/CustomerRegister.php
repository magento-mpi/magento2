<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CustomerRegister
{
    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

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
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
        $this->_logger = $logger;
    }

    /**
     * Update reward points after customer register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }
        /* @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        $customerOrigData = $customer->getOrigData();
        if (empty($customerOrigData)) {
            try {
                $subscribeByDefault = $this->_rewardData->getNotificationConfig(
                    'subscribe_by_default',
                    $this->_storeManager->getStore()->getWebsiteId()
                );
                $reward = $this->_rewardFactory->create()->setCustomer(
                    $customer
                )->setActionEntity(
                    $customer
                )->setStore(
                    $this->_storeManager->getStore()->getId()
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_REGISTER
                )->updateRewardPoints();

                $customer->setRewardUpdateNotification(
                    (int)$subscribeByDefault
                )->setRewardWarningNotification(
                    (int)$subscribeByDefault
                );
                $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
                $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
            } catch (\Exception $e) {
                //save exception if something were wrong during saving reward and allow to register customer
                $this->_logger->logException($e);
            }
        }
        return $this;
    }
}
