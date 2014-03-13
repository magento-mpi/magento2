<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Quote;

use \Magento\Sales\Model\Quote;

/**
 * Class Filter
 */
class Filter
{
    /**
     * Whether there are items with recurring payment
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function hasRecurringItems(Quote $quote)
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getProduct() && $item->getProduct()->getIsRecurring()) {
                return true;
            }
        }
        return false;
    }
}
