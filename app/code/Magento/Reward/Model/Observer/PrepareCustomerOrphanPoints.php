<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class PrepareCustomerOrphanPoints
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     */
    public function __construct(
        \Magento\Reward\Model\RewardFactory $rewardFactory
    ) {
        $this->_rewardFactory = $rewardFactory;
    }

    /**
     * Prepare orphan points of customers after website was deleted
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $website \Magento\Store\Model\Website */
        $website = $observer->getEvent()->getWebsite();
        $this->_rewardFactory->create()->prepareOrphanPoints($website->getId(), $website->getBaseCurrencyCode());
        return $this;
    }
}
