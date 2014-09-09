<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

class ModifyExpiredQuotesCleanup
{
    /**
     * Modify expired quotes cleanup
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute($observer)
    {
        /** @var $salesObserver \Magento\Sales\Model\Observer */
        $salesObserver = $observer->getEvent()->getSalesObserver();
        $salesObserver->setExpireQuotesAdditionalFilterFields(array('is_persistent' => 0));
    }
}
