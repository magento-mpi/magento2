<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\PersistentHistory\Model\Observer;

/**
 * Class CleanExpiredQuotesPlugin
 */
class CleanExpiredQuotesPlugin
{
    /**
     * @param \Magento\Sales\Model\Observer\CleanExpiredQuotes $subject
     * @return void
     */
    public function beforeExecute(\Magento\Sales\Model\Observer\CleanExpiredQuotes $subject)
    {
        $subject->setExpireQuotesAdditionalFilterFields(['is_persistent' => 0]);
    }
}
