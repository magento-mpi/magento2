<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CheckRates
{
    /**
     * Reward rate factory
     *
     * @var \Magento\Reward\Model\Reward\RateFactory
     */
    protected $_rateFactory;

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
     * @param \Magento\Reward\Model\Reward\RateFactory $rateFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\Reward\RateFactory $rateFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_rateFactory = $rateFactory;
    }

    /**
     * If not all rates found, we should disable reward points on frontend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }

        $groupId = $observer->getEvent()->getCustomerSession()->getCustomerGroupId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $rate = $this->_rateFactory->create();

        $hasRates = $rate->fetch(
            $groupId,
            $websiteId,
            \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
        )->getId() && $rate->reset()->fetch(
            $groupId,
            $websiteId,
            \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
        )->getId();

        $this->_rewardData->setHasRates($hasRates);

        return $this;
    }
}
