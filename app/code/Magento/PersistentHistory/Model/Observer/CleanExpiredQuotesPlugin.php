<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $subject->setExpireQuotesAdditionalFilterFields(array('is_persistent' => 0));
    }
}
