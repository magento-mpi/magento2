<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class LoadRewardSalesruleData
{
    /**
     * Reward factory
     *
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
     * Set reward points delta to salesrule model after it loaded
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
        if ($salesRule->getId()) {
            $data = $this->_rewardResourceFactory->create()->getRewardSalesrule($salesRule->getId());
            if (isset($data['points_delta'])) {
                $salesRule->setRewardPointsDelta($data['points_delta']);
            }
        }
        return $this;
    }
}
