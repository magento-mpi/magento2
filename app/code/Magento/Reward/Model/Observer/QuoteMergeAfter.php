<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class QuoteMergeAfter
{
    /**
     * Set use reward points flag to new quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $source = $observer->getEvent()->getSource();

        if ($source->getUseRewardPoints()) {
            $quote->setUseRewardPoints($source->getUseRewardPoints());
        }
        return $this;
    }
}
