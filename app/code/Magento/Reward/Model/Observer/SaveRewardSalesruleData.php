<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class SaveRewardSalesruleData
{
    /**
     * Reward factory
     * @var \Magento\Reward\Model\Resource\RewardFactory
     */
    protected $_rewardResourceFactory;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\Resource\RewardFactory $rewardResourceFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\Resource\RewardFactory $rewardResourceFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_rewardResourceFactory = $rewardResourceFactory;
    }

    /**
     * Save reward points delta for salesrule
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        /* @var $salesRule \Magento\SalesRule\Model\Rule */
        $salesRule = $observer->getEvent()->getRule();
        $this->_rewardResourceFactory->create()->saveRewardSalesrule(
            $salesRule->getId(),
            (int)$salesRule->getRewardPointsDelta()
        );
        return $this;
    }
}
