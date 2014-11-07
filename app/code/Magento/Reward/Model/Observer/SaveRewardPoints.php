<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

use Magento\Customer\Model\Converter;

/**
 * Class SaveRewardPoints
 */
class SaveRewardPoints
{
    /**
     * Customer converter
     *
     * @var Converter
     */
    protected $_customerConverter;

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
     * @param Converter $customerConverter
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        Converter $customerConverter
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
        $this->_customerConverter = $customerConverter;
    }

    /**
     * Update reward points for customer, send notification
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute($observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }

        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost('reward');
        if ($data && !empty($data['points_delta'])) {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            $customer = $observer->getEvent()->getCustomer();

            if (!isset($data['store_id'])) {
                if ($customer->getStoreId() == 0) {
                    $data['store_id'] = $this->_storeManager->getDefaultStoreView()->getStoreId();
                } else {
                    $data['store_id'] = $customer->getStoreId();
                }
            }
            $customerModel = $this->_customerConverter->getCustomerModel($customer->getId());
            /** @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_rewardFactory->create();
            $reward->setCustomer($customerModel)
                ->setWebsiteId($this->_storeManager->getStore($data['store_id'])->getWebsiteId())
                ->loadByCustomer();

            $reward->addData($data);
            $reward->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_ADMIN)
                ->setActionEntity($customerModel)
                ->updateRewardPoints();
        }

        return $this;
    }
}
