<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class ScheduledPointsExpiration
{
    /**
     * Reward history factory
     *
     * @var \Magento\Reward\Model\Resource\Reward\HistoryFactory
     */
    protected $_historyItemFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\Resource\Reward\HistoryFactory $_historyItemFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\Resource\Reward\HistoryFactory $_historyItemFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_historyItemFactory = $_historyItemFactory;
    }

    /**
     * Make points expired
     *
     * @return $this
     */
    public function execute()
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        foreach ($this->_storeManager->getWebsites() as $website) {
            if (!$this->_rewardData->isEnabledOnFront($website->getId())) {
                continue;
            }
            $expiryType = $this->_rewardData->getGeneralConfig('expiry_calculation', $website->getId());
            $this->_historyItemFactory->create()->expirePoints($website->getId(), $expiryType, 100);
        }

        return $this;
    }
}
